<?php declare(strict_types = 1); // atom

namespace PHPToolBucket\CompressedIntSet;

//[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

use function array_key_exists;

//[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

class CompressedIntSet
{
    public $ranges;

    function __construct(){
        $this->ranges = [];
    }

    function equals($other){
        if(!$other instanceof CompressedIntSet){
            return FALSE;
        }

        if(count($this->ranges) !== count($other->ranges)){
            return FALSE;
        }

        foreach($this->ranges as $start => $end){
            if(
                isset($other->ranges[$start]) === FALSE ||
                $other->ranges[$start] !== $end
            ){
                return FALSE;
            }
        }

        return TRUE;
    }

    function contains(Int $element){
        foreach($this->ranges as $start => $end){
            if($element >= $start && $element <= $end){
                return TRUE;
            }
        }
        return FALSE;
    }

    function containsAll(CompressedIntSet $elements){
        foreach($elements->ranges as $sStart => $sEnd){
            foreach($this->ranges as $start => $end){
                if($sStart >= $start && $sEnd <= $end){
                    continue 2;
                }
            }
            return FALSE;
        }
        return TRUE;
    }

    function remove(Int $element){
        if(array_key_exists($element, $this->ranges)){
            if($this->ranges[$element] !== $element){
                $this->ranges[$element + 1] = $this->ranges[$element];
            }
            unset($this->ranges[$element]);
        }

        foreach($this->ranges as $start => $end){
            if($element >= $start && $element <= $end){
                assert($element !== $start); // this can never happen as it's solved before the foreach
                if($element === $end){
                    $this->ranges[$start] = $element - 1;
                    return;
                }
                $this->ranges[$element + 1] = $end;
                $this->ranges[$start] = $element - 1;
                return;
            }
        }
    }

    function add(Int $element){
        if(array_key_exists($element, $this->ranges)){
            return;
        }

        foreach($this->ranges as $start => $end){
            if($element < $start){ continue; }
            if($element <= $end){ return; }

            if($end + 1 === $element){
                if(array_key_exists($element + 1, $this->ranges)){
                    $newEnd = $this->ranges[$element + 1];
                    $this->ranges[$start] = $newEnd;
                    unset($this->ranges[$element + 1]);
                    return;
                }
                $this->ranges[$start] = $element;
                return;
            }
        }

        if(array_key_exists($element + 1, $this->ranges)){
            $this->ranges[$element] = $this->ranges[$element + 1];
            unset($this->ranges[$element + 1]);
            return ;
        }

        $this->ranges[$element] = $element;
    }

    function addRange(Int $addStart, Int $addEnd){
        assert($addStart <= $addEnd);
        while(TRUE){
            restart:
            foreach($this->ranges as $existingStart => $existingEnd){
                // If finds an overlapping existing range, removes it and adds it to
                // $addStart and $addEnd, which is then re-added after the loop
                if(
                    ($addEnd + 1 >= $existingStart && $addStart <= $existingStart) ||
                    ($addStart - 1 <= $existingEnd && $addEnd >= $existingEnd) ||
                    ($addStart >= $existingStart && $addEnd <= $existingEnd)
                ){
                    unset($this->ranges[$existingStart]);
                    $addStart = min($addStart, $existingStart);
                    $addEnd = max($addEnd, $existingEnd);
                    goto restart; // cannot `continue`, needs to reload $this->ranges after the changes
                }
            }
            break;
        }
        $this->ranges[$addStart] = $addEnd;
    }

    function removeRange(Int $removeStart, Int $removeEnd){
        // This function is well designed - the ones above should be refactored a bit
        assert($removeStart <= $removeEnd);
        foreach($this->ranges as $existingStart => $existingEnd){
            if($removeStart > $existingStart && $removeEnd < $existingEnd){
                // Deletes an inner range (doesn't touch margins of the existing range)
                //           |__________|               existing range
                //             |______|                 remove ^
                //               |____|                 remove ^
                $beforeStart = $existingStart;
                $beforeEnd = $removeStart - 1;
                $afterStart = $removeEnd + 1;
                $afterEnd = $existingEnd;
                unset($this->ranges[$existingStart]);
                $this->ranges[$beforeStart] = $beforeEnd;
                $this->ranges[$afterStart] = $afterEnd;
            }elseif($removeStart <= $existingStart && $removeEnd >= $existingEnd){
                // Full wrap of the range - deletes an identical range or larger, ie the whole range
                //                     |________|       existing range
                //                     |________|       remove ^
                //                  |___________|       remove ^
                //                     |______________| remove ^
                //               |____________________|
                unset($this->ranges[$existingStart]);
            }elseif($removeStart <= $existingStart && $removeEnd >= $existingStart){
                // Deletes the start of the range except 1 or more on its right (the x)
                //                     |__________x|    existing range
                //                |______________|      remove ^
                //                   |_________|        remove ^
                //            |______________|          remove ^
                $newStart = $removeEnd + 1;
                $newEnd = $existingEnd;
                unset($this->ranges[$existingStart]);
                $this->ranges[$newStart] = $newEnd; // this is x or larger
            }elseif($removeEnd >= $existingEnd && $removeStart <= $existingEnd){
                // Deletes the end of the range except 1 or more on its left (the x)
                //       |x_________|                   existing range
                //         |______________|             remove ^
                //           |_________|                remove ^
                //             |______________|         remove ^
                $newStart = $existingStart;
                $newEnd = $removeStart - 1;
                unset($this->ranges[$existingStart]);
                $this->ranges[$newStart] = $newEnd; // this is x or larger
            }
        }
    }
}
