<?php

declare(strict_types=1);

namespace IContent\Console;

use Carbon\Carbon;
use Exception;
use IContent\Services\ContentExtractor\ContentExtractorService;
use IContent\Services\LinksExtractor\LinksExtractorFactory;
use IContent\Services\Network\NetworkService;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadContent extends Command
{
    protected static $defaultName = 'download';
    
    const TEXT_MODE_HTML = 'html';
    const TEXT_MODE_TEXT = 'text';
    const SAVE_MODE_SINGLE = 'single';
    const SAVE_MODE_MULTI = 'multi';
    
    /**
     * @var \IContent\Services\LinksExtractor\LinksExtractorInterface
     */
    private $linksExtractor;
    /**
     * @var ContentExtractorService
     */
    private $contentExtractor;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var string
     */
    private $textMode;
    /**
     * @var string
     */
    private $saveMode;
    /**
     * @var string
     */
    private $saveTo;
    
    /**
     * @var Carbon
     */
    private $startAt;
    
    /**
     * DownloadContent constructor.
     */
    public function __construct()
    {
        $this->linksExtractor = LinksExtractorFactory::make('sitemap');
        $network = new NetworkService();
        $this->contentExtractor = new ContentExtractorService($network);
        parent::__construct();
    }
    
    
    protected function configure()
    {
        $this->setDescription("Показать список добавленных сайтов");
        $this->addOption('url', 'u', InputOption::VALUE_REQUIRED, 'Ссылка на sitemap.xml');
        $this->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Файл с ссылками на sitemap.xml');
        $this->addOption('text-mode', 'm', InputOption::VALUE_REQUIRED, 'Режим сохранения текста html/text', static::TEXT_MODE_TEXT);
        $this->addOption('save-mode', 's', InputOption::VALUE_REQUIRED, 'Режим сохранения текста html/text', static::SAVE_MODE_SINGLE);
        $this->addOption('save-to', null, InputOption::VALUE_REQUIRED, 'Путь для сохранения результата. (путь к файлу или к папке в зависимости от выбранного save-mode');
        $this->addOption('user-agent', 'g', InputOption::VALUE_REQUIRED, 'Указать свой User Agent');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getOption('url');
        $file = $input->getOption('file');
        $this->textMode = $input->getOption('text-mode');
        $this->saveMode = $input->getOption('save-mode');
        $this->saveTo = $input->getOption('save-to');
        $this->output = $output;
        $this->startAt = Carbon::now();
        $userAgent = $input->getOption('user-agent') ?? '';
        $this->contentExtractor->setUserAgent($userAgent);
        $this->linksExtractor->setUserAgent($userAgent);
        
        $this->showHeader();
        
        if (!empty($url)) {
            $this->downloadUrl($url);
        } else if (!empty($file)) {
            $this->processFile($file);
        } else {
            $this->output->writeln("Ошибка: необходимо указать одну из опций url или file!");
        }
        
        $now = Carbon::now();
        $this->output->writeln("Закончили обработку [" . $now->toDateTimeString() . "]");
        $this->output->writeln("Затраченное время: " . $now->diffInMinutes($this->startAt) . " минут");
    }
    
    private function processFile(string $filename)
    {
        $links = file($filename);
        $links = array_map('trim', $links);
        
        foreach ($links as $url) {
            $this->downloadUrl($url);
        }
    }
    
    private function downloadUrl(string $url)
    {
        $this->output->writeln("===============================================");
        $this->output->writeln("Начинаем обработку: [{$url}]");
        $this->output->writeln("Получаем список ссылок с источника: [{$url}]");
        
        if (Str::endsWith($url, '.xml')) {
            $links = $this->linksExtractor->extractLinksFromOrigin($url);
            if ($links->isEmpty()) {
                $this->output->writeln("Ничего не нашли.");
                return;
            }
    
            $links_count = $links->count();
        } else {
            $links_count = 1;
            $links[] = $url;
        }
        
        $this->output->writeln("Нашли всего {$links_count} ссылок.");
    
        $i = 1;
        $this->output->writeln("");
        foreach ($links as $link) {
            try
            {
                $content = $this->contentExtractor->extract($link);
                $content->content = str_replace('><', '> <', $content->content);
                if ($this->textMode === static::TEXT_MODE_TEXT) {
                    $content->content = strip_tags($content->content);
                    $content->content = preg_replace("~\s+~", " ", $content->content);
                }
                
                $this->saveContent($content);
                
                $this->output->writeln("\e[0A\rОбработано [{$i}/{$links_count}]");
                $i++;
            } catch (Exception $e) {
                $this->output->writeln("Ошибка обработки ссылки: \"{$link}\". {$e->getMessage()}");
                $this->output->writeln("");
            }
        }
    }
    
    
    
    private function saveContent(stdClass $content)
    {
        switch ($this->saveMode) {
            case static::SAVE_MODE_SINGLE:
                $this->saveSingle($content);
                break;
            case static::SAVE_MODE_MULTI:
                $this->saveMulti($content);
                break;
            default:
                throw new \RuntimeException("Неправильный save-mode '{$this->saveMode}'");
        }
    }
    
    private function saveSingle(stdClass $content)
    {
        if (empty($this->saveTo)) {
            $this->saveTo = ROOT_PATH . '/content.txt';
        }
        
        file_put_contents($this->saveTo, $content->content . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    private function saveMulti(stdClass $content)
    {
        if (empty($this->saveTo)) {
            $this->saveTo = ROOT_PATH . '/content';
        }
        
        $saveTo = $this->saveTo . DIRECTORY_SEPARATOR . $this->startAt->format('Y-m-d_H-i');
        if (!is_dir($saveTo)) {
            mkdir($saveTo, 0777, true);
        }
        
        $title = preg_replace("~[^a-zA-Zа-яА-Я0-9 ]~uis", "", $content->title);
        $title = preg_replace("~\s+~", " ", $title);
        $filename = $saveTo . DIRECTORY_SEPARATOR . $title . '.txt';
        
        file_put_contents($filename, $content->content,LOCK_EX);
    }
    
    private function showHeader()
    {
        $this->output->writeln(sprintf("\t\t\tSDL Translation v%s", "0.1"));
        $this->output->writeln("\t\t\tAuthor:  NoHate");
        $this->output->writeln("\t\t\tContact: @PlzDontHate");
        $this->output->writeln('');
        
        $this->output->writeln("Начинаем работу [" . $this->startAt->toDateTimeString() . "]");
    }
}