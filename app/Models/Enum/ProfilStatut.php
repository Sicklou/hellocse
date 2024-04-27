<?php

namespace App\Models\Enum;

enum ProfilStatut: string
{
    case Actif = 'actif';

    case Inactif = 'inactif';

    case EnAttente = 'en attente';

}
