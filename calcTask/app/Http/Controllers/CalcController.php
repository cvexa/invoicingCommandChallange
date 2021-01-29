<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalcController extends Controller
{
    public $invoices;
    public $credits;
    public $debits;
    public $currencies;
    public $result;
    public $possibleCurrencies;

    public function formProcess(Request $request)
    {
        $data = $request->validate([
            'csvFile' => 'required|file|mimes:csv,txt',
            'currencies' => 'required',
            'outputCurrency' => 'required',
            'customer' => 'sometimes',//vat number
        ]);

        $file = $request->file('csvFile');
        $fileRealName = $file->getClientOriginalName();
        $temp_path = base_path() . '/public/temp/';
        $file->move($temp_path, $fileRealName);

        if(!$this->setData($temp_path . $fileRealName, $data['customer'])){
            return redirect()->back()->withErrors(['Requested VAT doesn"t exists in the document']);
        }
        if(!$this->validateCurrencies($data['outputCurrency'])){
            return redirect()->back()->withErrors(['Requested output currency its not supported']);
        }
        if(!$this->setCurrencies($request->currencies)){
            return redirect()->back()->withErrors(['Requested currencies are not in the right format please use CUR:NUM,']);
        }
        $this->calcTotal($data['outputCurrency']);

        unlink($temp_path . $fileRealName);

        return redirect()->back()->with('success', ['result' => $this->result,'currency' => $data['outputCurrency']]);
    }

    //if requested output currency is not one of the currencies in the document
    public function validateCurrencies($expectedCurrency)
    {
        if(in_array($expectedCurrency,$this->possibleCurrencies)){
            return true;
        }
        return false;
    }

    public function setCurrencies($currencies)
    {
        $currencies = explode(",", $currencies);
        foreach ($currencies as $pos => $curr) {
            $temp = explode(":", $curr);
            if(!isset($temp[1])) {
                return false;
            }
            $result[$temp[0]] = (float)$temp[1];
            $this->currencies[$temp[0]] = (float)$temp[1];
        }
        return true;
    }

    //0 - customer, 1 - vat , 2 - document number, 3 - type , 4 - parent document , 5 - currency , 6 - total
    public function setData($path, $vat = null)
    {
        $file = fopen($path, "r");
        $dataArr = [];
        $i = 0;
        $firstline = true;
        while (($filedata = fgetcsv($file, 100000, ",")) !== false) {
            $num = count($filedata);
            if ($firstline) {
                $firstline = false;
                continue;
            }
            for ($c = 0; $c < $num; $c++) {
                if ($filedata[3] == 1) {//invoice type
                    $this->invoices[$filedata [2]][] = $filedata [$c];
                }
                if ($filedata[3] == 2) {//credit type
                    $this->credits[$filedata [1]] = $filedata[$c];
                }
                if ($filedata[3] == 3) {//debit type
                    $this->debits[$filedata [1]] = $filedata[$c];
                }
                $this->possibleCurrencies[] = $filedata[5];
            }
            $i++;
        }
        fclose($file);
        if (!is_null($vat)) {
            //taking the invoices only with the requested VAT (Customer)
            $requestedInvoices = array_filter($this->invoices, function ($v, $k) use ($vat) {
                return $v[1] == $vat && $v[3] == 1;
            }, ARRAY_FILTER_USE_BOTH);

            if (!empty($requestedInvoices)) {
                $this->invoices = $requestedInvoices;
                return true;
            }
            //if vat not found in the document return false, to trow error
            return false;
        }

        return true;
    }

    public function calcTotal($outputCurrency)
    {
        $customers = [];
        foreach ($this->invoices as $docNum => $invoice) {
            $base = (float)$invoice[6];
            $reqCurrency = round(($base * $this->currencies[$outputCurrency]), 2);

            if (!isset($customers[$invoice[1]])) {
                $customers[$invoice[1]] = $reqCurrency;
            } else {
                $customers[$invoice[1]] += $reqCurrency;
            }
            //storing customer name, by a vat key
            $vatCustomer[$invoice[1]] = $invoice[0];
        }

        foreach($customers as $vat => $total){
            if (isset($this->debits[$vat])) {
                $baseDebit = $this->debits[$vat];
                $debitsSum = round(($baseDebit * $this->currencies[$outputCurrency]), 2);
                $customers[$vat] += $debitsSum;
            }
            if (isset($this->credits[$vat])) {
                $baseCredit = $this->credits[$vat];
                $creditSum = round(($baseCredit * $this->currencies[$outputCurrency]), 2);
                $customers[$vat] -= $creditSum;
            }
            if($customers[$vat] < 0){//if the sum result is negative cast it to 0
                $customers[$vat] = 0;
            }
        }
        //matching the customer name with the sum data
        $this->result = array_combine(
            array_values($vatCustomer),
            array_values($customers)
        );

        return true;
    }
}
