<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use Illuminate\Http\Request;

class ListsController extends Controller
{
    private const SUV_VEHICLE = "000003";
    private const FIFITEEN_PERCENT_DISCOUNT = "85/100";
    private const THIRTY_PERCENT_DISCOUNT = "70/100";

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $FilteredCategory = $request->input('category');
        $FilteredPrice = $request->input('price');
        $FilteredPriceEnabled = "off";
        $DatabaseProducts = Lists::get();

        if ($FilteredCategory !== 'NULL') {
            $DatabaseProducts = Lists:: where('category', $FilteredCategory)->get();
        } elseif ($FilteredPrice !== 'NULL') {
            $DatabaseProducts = Lists:: where('price', $FilteredPrice)->get();
            $FilteredPriceEnabled = "on";
        }
        $products = [];
        foreach ($DatabaseProducts as $data) {
            $ItemCategory = $data["category"];
            $ItemSku = $data["sku"];
            $PriceOriginal = $PriceFinal = $data["price"];
            $DiscountPercentage = 'NULL';
            if ($ItemCategory == "insurance") {
                $PriceFinal = (int)($PriceOriginal) * self::THIRTY_PERCENT_DISCOUNT;
                $DiscountPercentage = "30%";
            }
            if ($ItemSku == self::SUV_VEHICLE) {
                $PriceFinal = (int)($PriceOriginal) * self::FIFITEEN_PERCENT_DISCOUNT;
                $DiscountPercentage = "15%";
            }
            if ($FilteredPriceEnabled == "on") {
                $price = $PriceOriginal;
            } else {
                $price = collect(['original' => $PriceOriginal, 'final' => $PriceFinal,'discount_percentage' => $DiscountPercentage, 'currency' => "EUR"]);
            }
            $product = collect(['sku' => $data["sku"], 'name' => $data["name"],'category' => $data["category"], 'price' => $price]);
            array_push($products, $product);
        }

        return $products;
    }
}