<?php

    // General count only
    if(1 == $mode) {
        echo $general;
    }

    // Unique count only
    if(2 == $mode) {
        echo $unique;
    }

    // Unique + General
    if(3 == $mode) {
        echo $unique . ' (' . $general . ')';
    }