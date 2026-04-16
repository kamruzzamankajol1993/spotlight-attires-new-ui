<?xml version="1.0" encoding="utf-8"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title><![CDATA[{{ $front_ins_name ?? 'Your Shop Name' }}]]></title>
        <link>{{ rtrim($front_ins_url, '/') }}</link>
        <description><![CDATA[{{ $front_ins_d ?? 'Product Catalog for Facebook Shop' }}]]></description>
        
        @foreach($products as $product)
            @php
                // Image URL Setup (AppServiceProvider থেকে আসা $front_ins_url দিয়ে)
                $imageUrl = (is_array($product->main_image) && count($product->main_image) > 0) 
                            ? rtrim($front_ins_url, '/') . '/public/uploads/' . $product->main_image[0] 
                            : rtrim($front_ins_url, '/') . '/public/images/default.png'; // ডিফল্ট ইমেজের পাথ

                // Product URL Setup
                $productUrl = rtrim($front_ins_front_url, '/') . '/product/' . $product->slug;

                // Price Setup (ডিসকাউন্ট থাকলে সেটা, না থাকলে বেস প্রাইস)
                $price = ($product->discount_price > 0) ? $product->discount_price : $product->base_price;
            @endphp
            
            <item>
                <g:id>{{ $product->id }}</g:id>
                <g:title><![CDATA[{{ $product->name }}]]></g:title>
                <g:description><![CDATA[{{ html_entity_decode(strip_tags($product->description)) }}]]></g:description>
                <g:link>{{ $productUrl }}</g:link>
                <g:image_link>{{ $imageUrl }}</g:image_link>
                <g:brand><![CDATA[{{ $product->brand->name ?? $front_ins_name ?? 'Custom Brand' }}]]></g:brand>
                <g:condition>new</g:condition>
                <g:availability>in stock</g:availability>
                <g:price>{{ $price }} BDT</g:price>
            </item>
        @endforeach
        
    </channel>
</rss>