<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ApprenantsErreursExport implements FromCollection, WithHeadings
{
    protected $failedApprenants;
    public function __construct(array $failedApprenants)
    {
        $this->failedApprenants = $failedApprenants;
    }
    public function collection()
    {
        return collect($this->failedApprenants);
    }
    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
            'Date de Naissance',
            'Sexe',
            'Email',
            'Commentaire',
        ];
    }
}