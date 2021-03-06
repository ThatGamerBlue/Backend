<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function login(){
    	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mojang.com/users/profiles/minecraft/".Request("username"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 
        if($httpcode == 200){
            $arr = json_decode($output,true);
            session(['username' => $arr["name"]]);
            session(['uuid' => $arr["id"]]);
        }

    	return redirect()->back();
    }
    public function logout(){
    	session()->forget('username');
    	return redirect()->back();
    }

    public function showCategory(Category $category){
        return view('category')->with(['category'=>$category]);
    }
    public function paymentDone(){
        // $item_name = $_POST['item_name'];
        // $item_number = $_POST['item_number'];
        // $payment_status = $_POST['payment_status'];
        // $payment_amount = $_POST['mc_gross'];
        // $payment_currency = $_POST['mc_currency'];
        // $txn_id = $_POST['txn_id'];
        // $receiver_email = $_POST['receiver_email'];
        // $payer_email = $_POST['payer_email'];
        // $order = \App\Order::find($item_number);
        // if($order == null){
        //     abort(404);
        //     return;
        // }
         return view('paymentDone');
        }
}
