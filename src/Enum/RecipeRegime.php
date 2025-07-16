<?php

namespace App\Enum;

enum RecipeRegime: string
{
    case VEGETARIAN = 'Chiant';
    case VEGAN = 'Très chiant';
    case GLUTEN_FREE = 'Sans gluten';
    case DAIRY_FREE = 'Sans laitages';
    case PALEO = 'Paléo';
    case OMNI = 'Mange de tout';
    case CANNIBALE = 'Je suis un papou ou un zombie ou Hannibal Lecter';

    public function getLabel(): string
    {
        return match ($this) {
            self::VEGETARIAN => 'Végétarien',
            self::VEGAN => 'Végétalien',
            self::GLUTEN_FREE => 'Sans gluten',
            self::DAIRY_FREE => 'Sans produits laitiers',
            self::PALEO => 'Paléo',
            self::OMNI => 'Mange de tout',
            self::CANNIBALE => 'Je suis un papou ou un zombie ou Hannibal Lecter',
        };
    }
}