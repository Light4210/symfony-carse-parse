<?php

namespace App\Command;

use App\Entity\Element;
use App\Entity\Additional;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\ElementRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdditionalRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ParseTestComand extends Command
{
    public function __construct(LoggerInterface $logger, MailerInterface $mailer, AdditionalRepository $additionalRepository, ElementRepository $elementRepository, EntityManagerInterface $entityManager, ContainerBagInterface $params, HttpClientInterface $client)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->mailer = $mailer;
        $this->client = $client;
        $this->additionalRepository = $additionalRepository;
        $this->elementRepository = $elementRepository;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        // Use in-build functions to set name, description and help
        $this->setName('parse-test')
            ->setDescription('Starting parse car parts site');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '1024M');
        $this->additionalRepository->deleteAll();
        $this->elementRepository->deleteAll();
        $dateFrom = '2010-11-01';
        $dateTo = '2010-12-01';
        $todayTimestamp = time();
        $cookies = $this->getLoginUserCookies();
        $cookies['ASP.NET_SessionId'] = 'suy0oe455o5lb0452oq0zjmz';
        $cookies = $this->formCookie($cookies);
        echo 'START' . PHP_EOL;
        for ($i = 0; $i < 50000; $i++){
            $additional = new Additional('test', 4);
            $element = new Element('test', 'test', 44, true, 'test', 4, 'test', 'test');
            $this->entityManager->persist($element);
            $this->entityManager->persist($additional);
            echo $i . PHP_EOL;
        }

        echo 'DATABASE UPDATE' . PHP_EOL;
        $this->entityManager->flush();
        echo 'DATABASE UPDATE END' . PHP_EOL;
        $this->saveElementToFile();
        $email = (new Email())
            ->from('tarnavskij2002@gmail.com')
            ->to('tarnavskij2002@gmail.com')
            ->subject('Таблиця деталей з ajsparts.pl')
            ->text('Таблиця')
            ->addPart(new DataPart(fopen($this->params->get('kernel.project_dir') . '/public/' . "elements.xlsx", 'r'), 'default.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
        $this->mailer->send($email);

        // Return below values according to the occurred situation
        return 1;
    }

    private function createSiteUrl(string $url)
    {
        return $this->params->get('site.url') . $url;
    }

    private function getLoginUserCookies()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://ajsparts.pl/pages/custompage.aspx?pageid=224');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "ctl00%24ctl05=ctl00%24box_6%24upLoginControl%7Cctl00%24box_6%24btnLogin&__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUKLTU1MTkwNjMzMA9kFgJmD2QWBAIBD2QWAgIFDxUEAS8cL2RvZGF0a2kvZnVuY3Rpb24vYXNjb0xpYi5qczYvQXBwX1RoZW1lcy9hanNwYXJ0cy9nbG9iYWwvanMvYmFubmVyX2ZhZGluZ19zbGlkZXIuanMuL0FwcF9UaGVtZXMvYWpzcGFydHMvZ2xvYmFsL2pzL2NsaWVudF90aGVtZS5qc2QCAw8WAh4FY2xhc3MFNmJvZHktcGFnZXMtY3VzdG9tcGFnZSBwYWdlaWQtMjI0IGxhbmctcnUgY3VsdHVyZS1ydS1SVRYCZg8WAh4GYWN0aW9uBTRodHRwczovL2Fqc3BhcnRzLnBsL3BhZ2VzL2N1c3RvbXBhZ2UuYXNweD9wYWdlaWQ9MjI0Fg4CBA9kFgJmD2QWAmYPZBYCAgEPZBYCAgEPZBYCZg9kFgJmD2QWAgIBD2QWBGYPDxYCHgdWaXNpYmxlaGRkAgIPFgIeC18hSXRlbUNvdW50Ag0WGmYPZBYCAgEPDxYIHgtOYXZpZ2F0ZVVybAU8aHR0cHM6Ly9hanNwYXJ0cy5wbC9wYWdlcy9jdXN0b21wYWdlLmFzcHg%2FcGFnZWlkPTIyNCZsYW5nPXBsHgdUb29sVGlwBQZQT0xTS0keCENzc0NsYXNzZR4EXyFTQgICZBYCZg8WBB4Dc3JjBSV%2BL0FwcF9UaGVtZXMvYWpzcGFydHMvbGFuZ3VhZ2UvcGwucG5nHgNhbHQFBlBPTFNLSWQCAQ9kFgICAQ8PFggfBAU8aHR0cHM6Ly9hanNwYXJ0cy5wbC9wYWdlcy9jdXN0b21wYWdlLmFzcHg%2FcGFnZWlkPTIyNCZsYW5nPWVuHwUFB0VOR0xJU0gfBmUfBwICZBYCZg8WBB8IBSV%2BL0FwcF9UaGVtZXMvYWpzcGFydHMvbGFuZ3VhZ2UvZW4ucG5nHwkFB0VOR0xJU0hkAgIPZBYCAgEPDxYIHwQFPGh0dHBzOi8vYWpzcGFydHMucGwvcGFnZXMvY3VzdG9tcGFnZS5hc3B4P3BhZ2VpZD0yMjQmbGFuZz1kZR8FBQdERVVUU0NIHwZlHwcCAmQWAmYPFgQfCAUlfi9BcHBfVGhlbWVzL2Fqc3BhcnRzL2xhbmd1YWdlL2RlLnBuZx8JBQdERVVUU0NIZAIDD2QWAgIBDw8WCB8EBTxodHRwczovL2Fqc3BhcnRzLnBsL3BhZ2VzL2N1c3RvbXBhZ2UuYXNweD9wYWdlaWQ9MjI0Jmxhbmc9ZnIfBQUJRlJBTsOHQUlTHwZlHwcCAmQWAmYPFgQfCAUlfi9BcHBfVGhlbWVzL2Fqc3BhcnRzL2xhbmd1YWdlL2ZyLnBuZx8JBQlGUkFOw4dBSVNkAgQPZBYCAgEPDxYIHwQFPGh0dHBzOi8vYWpzcGFydHMucGwvcGFnZXMvY3VzdG9tcGFnZS5hc3B4P3BhZ2VpZD0yMjQmbGFuZz1lcx8FBQhFU1BBw5FPTB8GZR8HAgJkFgJmDxYEHwgFJX4vQXBwX1RoZW1lcy9hanNwYXJ0cy9sYW5ndWFnZS9lcy5wbmcfCQUIRVNQQcORT0xkAgUPZBYCAgEPDxYIHwQFPGh0dHBzOi8vYWpzcGFydHMucGwvcGFnZXMvY3VzdG9tcGFnZS5hc3B4P3BhZ2VpZD0yMjQmbGFuZz1pdB8FBQhJVEFMSUFOTx8GZR8HAgJkFgJmDxYEHwgFJX4vQXBwX1RoZW1lcy9hanNwYXJ0cy9sYW5ndWFnZS9pdC5wbmcfCQUISVRBTElBTk9kAgYPZBYCAgEPDxYIHwQFPGh0dHBzOi8vYWpzcGFydHMucGwvcGFnZXMvY3VzdG9tcGFnZS5hc3B4P3BhZ2VpZD0yMjQmbGFuZz1lbB8FBRDOlc6bzpvOl86dzpnOms6RHwZlHwcCAmQWAmYPFgQfCAUlfi9BcHBfVGhlbWVzL2Fqc3BhcnRzL2xhbmd1YWdlL2VsLnBuZx8JBRDOlc6bzpvOl86dzpnOms6RZAIHD2QWAgIBDw8WCB8EBTxodHRwczovL2Fqc3BhcnRzLnBsL3BhZ2VzL2N1c3RvbXBhZ2UuYXNweD9wYWdlaWQ9MjI0Jmxhbmc9cnUfBQUO0KDQo9Ch0KHQmtCY0JkfBgUNbGFuZy1zZWxlY3RlZB8HAgJkFgJmDxYEHwgFJX4vQXBwX1RoZW1lcy9hanNwYXJ0cy9sYW5ndWFnZS9ydS5wbmcfCQUO0KDQo9Ch0KHQmtCY0JlkAggPZBYCAgEPDxYIHwQFPGh0dHBzOi8vYWpzcGFydHMucGwvcGFnZXMvY3VzdG9tcGFnZS5hc3B4P3BhZ2VpZD0yMjQmbGFuZz1maR8FBQtTVU9NQUxBSU5FTh8GZR8HAgJkFgJmDxYEHwgFJX4vQXBwX1RoZW1lcy9hanNwYXJ0cy9sYW5ndWFnZS9maS5wbmcfCQULU1VPTUFMQUlORU5kAgkPZBYCAgEPDxYIHwQFPGh0dHBzOi8vYWpzcGFydHMucGwvcGFnZXMvY3VzdG9tcGFnZS5hc3B4P3BhZ2VpZD0yMjQmbGFuZz1zdh8FBQdTVkVOU0tBHwZlHwcCAmQWAmYPFgQfCAUlfi9BcHBfVGhlbWVzL2Fqc3BhcnRzL2xhbmd1YWdlL3N2LnBuZx8JBQdTVkVOU0tBZAIKD2QWAgIBDw8WCB8EBTxodHRwczovL2Fqc3BhcnRzLnBsL3BhZ2VzL2N1c3RvbXBhZ2UuYXNweD9wYWdlaWQ9MjI0Jmxhbmc9aHUfBQUGTUFHWUFSHwZlHwcCAmQWAmYPFgQfCAUlfi9BcHBfVGhlbWVzL2Fqc3BhcnRzL2xhbmd1YWdlL2h1LnBuZx8JBQZNQUdZQVJkAgsPZBYCAgEPDxYIHwQFPGh0dHBzOi8vYWpzcGFydHMucGwvcGFnZXMvY3VzdG9tcGFnZS5hc3B4P3BhZ2VpZD0yMjQmbGFuZz1ybx8FBQlST03Dgk5FU0MfBmUfBwICZBYCZg8WBB8IBSV%2BL0FwcF9UaGVtZXMvYWpzcGFydHMvbGFuZ3VhZ2Uvcm8ucG5nHwkFCVJPTcOCTkVTQ2QCDA9kFgICAQ8PFggfBAU8aHR0cHM6Ly9hanNwYXJ0cy5wbC9wYWdlcy9jdXN0b21wYWdlLmFzcHg%2FcGFnZWlkPTIyNCZsYW5nPWNzHwUFB8SMRVNLw4EfBmUfBwICZBYCZg8WBB8IBSV%2BL0FwcF9UaGVtZXMvYWpzcGFydHMvbGFuZ3VhZ2UvY3MucG5nHwkFB8SMRVNLw4FkAgcPZBYEZg8PFgIfAmhkFgJmD2QWAmYPZBYCAgEPZBYCAgEPZBYCZg9kFgJmD2QWBgIBD2QWAgIDDxBkZBYAZAIGD2QWAgIBDxBkZBYAZAIID2QWAgIBD2QWAgIDDxBkZBYAZAICD2QWAmYPZBYCZg9kFgICAQ9kFgICAQ9kFgJmD2QWAmYPZBYCAgEPZBYCZg8PFgQfBmUfBwICZBYCAgEPDxYEHwYFFG5ld1NsaWRlciBzbGlkZXJJZC0xHwcCAmRkAggPFgIeBFRleHQFD1N0cm9uYSBnxYLDs3duYWQCCQ9kFgJmDxYCHwMCAxYEAgEPZBYCZg8VBAEwS2l0ZW1wcm9wPSJpdGVtTGlzdEVsZW1lbnQiIGl0ZW1zY29wZSBpdGVtdHlwZT0iaHR0cHM6Ly9zY2hlbWEub3JnL0xpc3RJdGVtIks8YSBocmVmPSIvIiBpdGVtcHJvcD0iaXRlbSI%2BPHNwYW4gaXRlbXByb3A9Im5hbWUiPtCT0LvQsNCy0L3QsNGPPC9zcGFuPjwvYT4BMWQCAw9kFgJmDxUEDzIgc25hdml0ZW0tbGFzdEtpdGVtcHJvcD0iaXRlbUxpc3RFbGVtZW50IiBpdGVtc2NvcGUgaXRlbXR5cGU9Imh0dHBzOi8vc2NoZW1hLm9yZy9MaXN0SXRlbSIsPHNwYW4gaXRlbXByb3A9Im5hbWUiPlN0cm9uYSBnxYLDs3duYTwvc3Bhbj4BMmQCCg9kFgJmDxYEHwMC%2F%2F%2F%2F%2Fw8fAmhkAgsPDxYEHwoFCtCd0LDQt9Cw0LQfAmdkZAIOD2QWAgIBDxYCHwplZGQ3XqQdp0wyZSgFSAX4nlpc8wgJHw%3D%3D&ctl00%24box_6%24tbUserId=006890&ctl00%24box_6%24tbPassword=12345678910&ctl00%24box_2%24hiddenSelection=&__VIEWSTATEGENERATOR=F636725F&__EVENTVALIDATION=%2FwEWBgKb17XDCQKGwbOpBgLF7r%2FlCAKdmJ7GAQKwjL6eBALj7ZDnCmj3gj2R0upUqv1v%2BQBNdw6LtqJY&__ASYNCPOST=true&ctl00%24box_6%24btnLogin=%D0%92%D0%BE%D0%B9%D1%82%D0%B8%20%D0%B2%20%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D1%83");
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $headers = array();
        $headers[] = 'Authority: ajsparts.pl';
        $headers[] = 'Accept: */*';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,ru-UA;q=0.8,ru;q=0.7,uk-UA;q=0.6,uk;q=0.5';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = 'Origin: https://ajsparts.pl';
        $headers[] = 'Referer: https://ajsparts.pl/';
        $headers[] = 'Sec-Ch-Ua: \"Not_A Brand\";v=\"99\", \"Google Chrome\";v=\"109\", \"Chromium\";v=\"109\"';
        $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
        $headers[] = 'Sec-Ch-Ua-Platform: \"macOS\"';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Cookie: ASP.NET_SessionId=suy0oe455o5lb0452oq0zjmz';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';
        $headers[] = 'X-Microsoftajax: Delta=true';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $login = curl_exec($ch);
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $login, $matches);
        $cookies = array();
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        return $cookies;
    }

    private function formCookie(array $cookies): string
    {
        $cookieHeader = '';
        foreach ($cookies as $name => $value) {
            $cookieHeader .= $name . '=' . $value . '; ';
        }

        return $cookieHeader;
    }

    private function saveElementToFile()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Users");
        $records = $this->elementRepository->getFullData();
        $i = 1;
        /** @var Element $row */
        foreach ($records as $row) {
            $row['availability'] = $row['availability'] ? 'Доступний' : 'Не доступний';
            $row['quantity'] = $row['quantity'] ?? 'Не відомо скільки є';
            $sheet->setCellValue("A" . $i, $row['elementId']);
            $sheet->setCellValue("B" . $i, $row['availability']);
            $sheet->setCellValue("C" . $i, $row['description']);
            $sheet->setCellValue("D" . $i, $row['price'] . ' ' . $row['currency']);
            $sheet->setCellValue("E" . $i, $row['name']);
            $sheet->setCellValue("F" . $i, $row['photoUrl']);
            $sheet->setCellValue("G" . $i, $row['additional']);
            $sheet->setCellValue("H" . $i, $row['quantity']);
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($this->params->get('kernel.project_dir') . '/public/' . "elements.xlsx");
        echo "OK";
    }
}