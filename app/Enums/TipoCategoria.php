<?php

namespace App\Enums;

enum TipoCategoria: string
{
    case ACESSORIO = 'AC';
    case ATIVO = 'AT';
    case CONSUMIVEL = 'CO';
    case LICENCA = 'LI';
    case COMPONENTE = 'CP';
}
