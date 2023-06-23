<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->transactions;
    }
    public function headings(): array
    {
        return [
            'Amount',
            'Bonus',
            'UTR No.',
            'Bank Holder Name',
            'Bank Name',
            'Date',
        ];
    }
}
