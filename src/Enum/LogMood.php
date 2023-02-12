<?php

namespace App\Enum;

enum LogMood: int
{
    case SupperGood = 7;
    case ReallyGood = 6;
    case Good = 5;
    case OK = 4;
    case Bad = 3;
    case ReallyBad = 2;
    case SupperBad = 1;
}
