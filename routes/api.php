<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\ArrayToXml\ArrayToXml;


Route::get('/', function (Request $request) {
    $url = $request->get('url');
    abort_if(!$url,404);
    $flat = $request->get('flat', false);
    $format = $request->get('format', "json");

    $html = file_get_contents($url);
    $doc = new DOMDocument();
    $doc->loadHTML($html);

    $items = $doc->getElementsByTagName('tr');

    $res = [];
    $header = [];
    /** @var DOMElement $node */
    foreach ($items as $node) {
        $row = [];
        foreach ($node->getElementsByTagName("td") as $element) {
            $row[] = trim($element->nodeValue);
        }

        if (!$header && !$flat) {
            $header = $row;
        } else {
            $res[] = $flat ? $row : array_combine($header, $row);
        }
    }

    if ($format == "json") {
        return $res;
    } elseif ($format == "xml") {
        //dd($res);
//        return Response::make(ArrayToXml::convert($res, "Live"), 200, [
//            'Content-Type' => "application/xml",
//        ]);
    } elseif ($format == "csv") {
        return response()->csv($res, 200, [], [
            'encoding' => "utf-8"
        ]);
    }

    abort(404);
});
