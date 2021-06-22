<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
	public function index(Request $request)
    {
    	$texts = [];
    	$texts['slideMenu'] = trans('slideMenu');
    	$texts['pageName'] = trans('pageName');
    	$texts['pageFn'] = trans('pageFunction');
    	$texts['msg'] = trans('message');
    	$texts = json_encode($texts);
    	return view('admin', compact('texts'));
    }

    public function info(Request $request)
    {
        //echo phpinfo();
    }
}