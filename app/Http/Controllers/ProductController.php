<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables; // Importando a classe DataTables

class ProductController extends Controller
{
    public function index()
    {
        $categories = [
            'ofertas-dia' => 'Ofertas do Dia',
            'ofertas-relampago' => 'Ofertas Relâmpago',
            'outlet' => 'Outlet',
            'celulares' => 'Celulares',
            'notebooks' => 'Notebooks',
            'menos-de-100' => 'Menos de R$100'
        ];

        return view('home', compact('categories'));
    }

    public function showCategory($category)
    {
        return view('category', compact('category'));
    }

    public function getProducts($category)
    {
        $products = Product::where('category', $category)->get();
        return DataTables::of($products)->make(true);
    }

    public function scrapeProducts()
    {
        try {
            Artisan::call('scrape:products');
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error scraping products: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}
