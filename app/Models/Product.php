<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Product extends Model
{
    use HasFactory;

    protected $table = 'products';


    protected $fillable = [
        'product_name',
        'company_id',
        'price',
        'stock',
        'comment',
        'img_path',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected $attributes = [
        'img_path' => 'images/dummy-product.png',
    ];

    public static function createProduct($data)
    {
        $imgPath = $data->hasFile('img_path')
            ? '/storage/' . $data->file('img_path')->storeAs('products', $data->file('img_path')->getClientOriginalName(), 'public')
            : 'images/dummy-product.png';

        self::create([
            'product_name' => $data->input('product_name'),
            'price' => $data->input('price'),
            'stock' => $data->input('stock'),
            'company_id' => $data->input('company_id'),
            'comment' => $data->input('comment'),
            'img_path' => $imgPath,
        ]);
    }

    public function updateProduct($data)
    {
        $imgPath = $data->hasFile('img_path')
            ? '/storage/' . $data->file('img_path')->storeAs('products', $data->file('img_path')->getClientOriginalName(), 'public')
            : $this->img_path;

        $this->update([
            'product_name' => $data->input('product_name'),
            'price' => $data->input('price'),
            'stock' => $data->input('stock'),
            'company_id' => $data->input('company_id'),
            'comment' => $data->input('comment'),
            'img_path' => $imgPath,
        ]);
    }

}



