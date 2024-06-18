<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Product;

class ScrapeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape product data from Mercado Livre';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(480);

        $this->info('Starting scraping...');

        $client = new Client([
            'verify' => false,
        ]);

        $categories = [
            'ofertas-dia' => 'https://www.mercadolivre.com.br/ofertas?container_id=MLB779362-1#origin=scut&filter_position=1&is_recommended_domain=false',
            'ofertas-relampago' => 'https://www.mercadolivre.com.br/ofertas?promotion_type=lightning&container_id=MLB779362-1#origin=scut&filter_applied=promotion_type&filter_position=2&is_recommended_domain=false',
            'outlet' => 'https://www.mercadolivre.com.br/ofertas?container_id=MLB916440-2#origin=scut&filter_position=3&is_recommended_domain=false',
            'celulares' => 'https://www.mercadolivre.com.br/ofertas?container_id=MLB779535-1&domain_id=MLB-CELLPHONES#origin=scut&filter_applied=domain_id&filter_position=4&is_recommended_domain=false',
            'notebooks' => 'https://www.mercadolivre.com.br/ofertas?container_id=MLB779536-1&domain_id=MLB-NOTEBOOKS#origin=scut&filter_applied=domain_id&filter_position=5&is_recommended_domain=false',
            'menos-de-100' => 'https://www.mercadolivre.com.br/ofertas?container_id=MLB779362-1&price=0.0-100.0#origin=scut&filter_applied=price&filter_position=6&is_recommended_domain=false',
        ];

        foreach ($categories as $category => $url) {
            $this->info("Scraping category: $category");

            try {
                $response = $client->request('GET', $url);
                $html = $response->getBody()->getContents();
                $this->info('Request successful. Processing data...');
            } catch (\Exception $e) {
                $this->error('Failed to fetch data: ' . $e->getMessage());
                continue;
            }

            $crawler = new Crawler($html);
            $count = 0;

            $crawler->filter('.promotion-item')->each(function (Crawler $node) use (&$count, $category) {
                if ($count < 100) {
                    $name = $node->filter('.promotion-item__title')->count() ? $node->filter('.promotion-item__title')->text() : 'N/A';
                    $priceFraction = $node->filter('.andes-money-amount__fraction')->count() ? $node->filter('.andes-money-amount__fraction')->text() : '0';
                    $priceCents = $node->filter('.andes-money-amount__cents')->count() ? $node->filter('.andes-money-amount__cents')->text() : '00';
                    $description = $node->filter('.promotion-item__description')->count() ? $node->filter('.promotion-item__description')->text() : 'No description';
                    $imageUrl = $node->filter('.promotion-item__img')->count() ? $node->filter('.promotion-item__img')->attr('src') : 'No image URL';

                    $price = floatval(str_replace(['.', ','], ['', '.'], "$priceFraction.$priceCents"));

                    Product::updateOrCreate(
                        ['name' => $name, 'category' => $category],
                        [
                            'price' => $price,
                            'description' => $description,
                            'image_url' => $imageUrl,
                            'category' => $category
                        ]
                    );

                    $this->info("Product: $name, Price: $price, Image URL: $imageUrl, Category: $category");

                    $count++;
                }
            });

            $this->info("Scraping for category $category completed. Total products fetched: $count");
        }

        $this->info('Scraping completed.');

        return Command::SUCCESS;
    }
}
