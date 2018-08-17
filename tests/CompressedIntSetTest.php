<?php declare(strict_types = 1); // atom

namespace PHPToolBucket\CompressedIntSetTests;

//[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

use PHPToolBucket\CompressedIntSet\CompressedIntSet;
use PHPUnit\Framework\TestCase;

//[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

class CompressedIntSetTest extends TestCase
{
    function test_contains(){
        $c = new CompressedIntSet();
        $c->ranges[24] = 42;
        $c->ranges[66] = 77;
        $c->ranges[88] = 99;

        self::assertFalse($c->contains(22));
        self::assertFalse($c->contains(23));

        self::assertTrue($c->contains(24));
        self::assertTrue($c->contains(25));
        self::assertTrue($c->contains(41));
        self::assertTrue($c->contains(42));

        self::assertFalse($c->contains(43));
        self::assertFalse($c->contains(44));

        self::assertFalse($c->contains(64));
        self::assertFalse($c->contains(65));

        self::assertTrue($c->contains(66));
        self::assertTrue($c->contains(67));
        self::assertTrue($c->contains(76));
        self::assertTrue($c->contains(77));

        self::assertFalse($c->contains(78));
        self::assertFalse($c->contains(79));

        self::assertFalse($c->contains(86));
        self::assertFalse($c->contains(87));

        self::assertTrue($c->contains(88));
        self::assertTrue($c->contains(89));
        self::assertTrue($c->contains(98));
        self::assertTrue($c->contains(99));

        self::assertFalse($c->contains(100));
        self::assertFalse($c->contains(101));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_containsAll_empty(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;
        $subject->ranges[33] = 44;
        self::assertTrue($subject->containsAll(new CompressedIntSet()));

        $subject = new CompressedIntSet();
        $search = new CompressedIntSet();
        $search->ranges[11] = 22;
        self::assertFalse($subject->containsAll($search));

        $subject = new CompressedIntSet();
        $search = new CompressedIntSet();
        self::assertTrue($subject->containsAll($search));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_containsAll_true_single_range(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;

        $search = new CompressedIntSet();
        foreach([11, 12, 13] as $s){
            foreach([22, 21, 20] as $e){
                $search->ranges = [$s => $e];
                self::assertTrue($subject->containsAll($search));
            }
        }
    }

    function test_containsAll_true_single_range_of_one_element(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 11;

        $search = new CompressedIntSet();
        $search->ranges[11] = 11;
        self::assertTrue($subject->containsAll($search));
    }

    function test_containsAll_false_single_range(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;

        $search = new CompressedIntSet();
        foreach([10, 9, 8] as $s){
            $search->ranges = [$s => 22];
            self::assertFalse($subject->containsAll($search));
        }

        $search = new CompressedIntSet();
        foreach([23, 24, 25] as $e){
            $search->ranges = [11 => $e];
            self::assertFalse($subject->containsAll($search));
        }
    }

    function test_containsAll_false_single_range_of_one_element(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 11;

        $search = new CompressedIntSet();
        $search->ranges[55] = 55;
        self::assertFalse($subject->containsAll($search));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_containsAll_true_multiple_ranges_in_subject(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;
        $subject->ranges[33] = 44;
        $subject->ranges[55] = 66;
        $search = new CompressedIntSet();

        foreach([11, 12, 13] as $s){
            foreach([22, 21, 20] as $e){
                $search->ranges = [$s => $e];
                self::assertTrue($subject->containsAll($search));
            }
        }

        foreach([33, 34, 35] as $s){
            foreach([44, 43, 42] as $e){
                $search->ranges = [$s => $e];
                self::assertTrue($subject->containsAll($search));
            }
        }

        foreach([55, 56, 57] as $s){
            foreach([66, 65, 64] as $e){
                $search->ranges = [$s => $e];
                self::assertTrue($subject->containsAll($search));
            }
        }
    }

    function test_containsAll_false_multiple_ranges_in_subject(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;
        $subject->ranges[33] = 44;
        $subject->ranges[55] = 66;
        $search = new CompressedIntSet();

        foreach([11, 12, 13] as $s){
            foreach([23, 24, 25] as $e){
                $search->ranges = [$s => $e];
                self::assertFalse($subject->containsAll($search));
            }
        }

        foreach([32, 31, 30] as $s){
            foreach([44, 44, 44] as $e){
                $search->ranges = [$s => $e];
                self::assertFalse($subject->containsAll($search));
            }
        }

        foreach([54, 53, 52] as $s){
            foreach([67, 68, 69] as $e){
                $search->ranges = [$s => $e];
                self::assertFalse($subject->containsAll($search));
            }
        }
    }

    function test_containsAll_true_multiple_ranges_in_search(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;
        $search = new CompressedIntSet();
        $search->ranges[11] = 13;
        $search->ranges[15] = 18;
        $search->ranges[20] = 22;
        self::assertTrue($subject->containsAll($search));
    }

    function test_containsAll_false_multiple_ranges_in_search(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;
        $search = new CompressedIntSet();
        $search->ranges[11] = 13;
        $search->ranges[15] = 18;
        $search->ranges[20] = 23;
        self::assertFalse($subject->containsAll($search));
    }

    function test_containsAll_multiple_ranges(){
        $subject = new CompressedIntSet();
        $subject->ranges[11] = 22;
        $subject->ranges[33] = 44;
        $subject->ranges[55] = 66;

        $search = new CompressedIntSet();
        $search->ranges[11] = 22;
        $search->ranges[33] = 44;
        $search->ranges[55] = 66;
        self::assertTrue($subject->containsAll($search));

        $search = new CompressedIntSet();
        $search->ranges[11] = 16;
        $search->ranges[18] = 22;
        $search->ranges[33] = 36;
        $search->ranges[38] = 44;
        $search->ranges[55] = 56;
        $search->ranges[58] = 66;
        self::assertTrue($subject->containsAll($search));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_equals(){
        $a = new CompressedIntSet();
        $a->ranges[22] = 33;
        $a->ranges[44] = 55;
        $a->ranges[66] = 77;

        $b = new CompressedIntSet();
        $b->ranges[66] = 77;
        $b->ranges[44] = 55;
        $b->ranges[22] = 33;

        self::assertTrue($a->equals($b));
        self::assertTrue($b->equals($a));

        $b->ranges[88] = 99;

        self::assertFalse($a->equals($b));
        self::assertFalse($b->equals($a));

        self::assertFalse($a->equals(42));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_add_existing(){
        $c = new CompressedIntSet();
        $c->ranges[22] = 33;
        $c->ranges[44] = 55;
        $c->ranges[66] = 77;

        $control = clone $c;

        $c->add(22);
        $c->add(23);
        $c->add(32);
        $c->add(33);

        $c->add(44);
        $c->add(45);
        $c->add(54);
        $c->add(55);

        $c->add(66);
        $c->add(67);
        $c->add(76);
        $c->add(77);

        self::assertTrue($c->equals($control));
    }

    function test_add_append_to_existing_range(){
        $c = new CompressedIntSet();
        $c->ranges[22] = 33;
        $c->ranges[44] = 55;
        $c->ranges[66] = 77;

        $control = clone $c;
        $control->ranges[44] = 56;

        $c->add(56);

        self::assertTrue($c->equals($control));
    }

    function test_add_prepend_to_existing_range(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[33] = 44;
        $c->ranges[55] = 66;

        $control = clone $c;
        unset($control->ranges[33]);
        $control->ranges[32] = 44;

        $c->add(32);

        self::assertTrue($c->equals($control));
    }

    function test_add_merges_two_ranges(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 34;
        $c->ranges[36] = 99;

        $control = clone $c;
        $control->ranges = [11 => 99];

        $c->add(35);

        self::assertTrue($c->equals($control));
    }

    function test_add_creates_new_range(){
        $c = new CompressedIntSet();
        $c->ranges[22] = 33;
        $c->ranges[44] = 55;
        $c->ranges[66] = 77;

        $control = clone $c;
        $control->ranges[88] = 88;

        $c->add(88);

        self::assertTrue($c->equals($control));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_remove_not_in_range(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[33] = 44;
        $c->ranges[55] = 66;

        $control = clone $c;

        foreach([10, 23, 32, 45, 54, 67] as $r){
            $c->remove($r);
            self::assertTrue($c->equals($control));
        }

    }

    function test_remove_range_of_one_single_element(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[33] = 33;
        $c->ranges[44] = 55;

        $control = clone $c;
        unset($control->ranges[33]);

        $c->remove(33);

        self::assertTrue($c->equals($control));
    }

    function test_remove_range_start(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[33] = 44;
        $c->ranges[55] = 66;

        $control = clone $c;
        unset($control->ranges[33]);
        $control->ranges[34] = 44;

        $c->remove(33);

        self::assertTrue($c->equals($control));
    }

    function test_remove_range_end(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[33] = 44;
        $c->ranges[55] = 66;

        $control = clone $c;
        $control->ranges[33] = 43;

        $c->remove(44);

        self::assertTrue($c->equals($control));
    }

    function test_remove_after_range_start(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[33] = 44;
        $c->ranges[55] = 66;

        $control = clone $c;
        $control->ranges[33] = 33;
        $control->ranges[35] = 44;

        $c->remove(34);

        self::assertTrue($c->equals($control));
    }

    function test_remove_before_range_end(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[33] = 44;
        $c->ranges[55] = 66;

        $control = clone $c;
        $control->ranges[44] = 44;
        $control->ranges[33] = 42;

        $c->remove(43);

        self::assertTrue($c->equals($control));
    }

    function test_remove_in_between_range(){
        $c = new CompressedIntSet();
        $c->ranges[11] = 22;
        $c->ranges[22] = 44;
        $c->ranges[55] = 66;

        $control = clone $c;
        $control->ranges[22] = 32;
        $control->ranges[34] = 44;

        $c->remove(33);

        self::assertTrue($c->equals($control));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_addRange_2_units_bigger(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //   |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(2, 5);
        $control = new CompressedIntSet();
        $control->ranges[2] = 5;
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //      |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(3, 6);
        $control = new CompressedIntSet();
        $control->ranges[3] = 6;
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //         |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(4, 7);
        $control = new CompressedIntSet();
        $control->ranges[4] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //            |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(5, 8);
        $control = new CompressedIntSet();
        $control->ranges[5] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //               |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(6, 9);
        $control = new CompressedIntSet();
        $control->ranges[6] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                  |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(7, 10);
        $control = new CompressedIntSet();
        $control->ranges[7] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                     |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(8, 11);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                        |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(9, 12);
        $control = new CompressedIntSet();
        $control->ranges[8] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                           |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(10, 13);
        $control = new CompressedIntSet();
        $control->ranges[8] = 13;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                              |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->addRange(11, 14);
        $control = new CompressedIntSet();
        $control->ranges[8] = 14;
        self::assertTrue($c->equals($control));
    }

    function test_addRange_1_unit_bigger(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //|_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(1, 6);
        $control = new CompressedIntSet();
        $control->ranges[1] = 6;
        $control->ranges[8] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //   |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(2, 7);
        $control = new CompressedIntSet();
        $control->ranges[2] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //      |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(3, 8);
        $control = new CompressedIntSet();
        $control->ranges[3] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //         |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(4, 9);
        $control = new CompressedIntSet();
        $control->ranges[4] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //            |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(5, 10);
        $control = new CompressedIntSet();
        $control->ranges[5] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //               |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(6, 11);
        $control = new CompressedIntSet();
        $control->ranges[6] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                  |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(7, 12);
        $control = new CompressedIntSet();
        $control->ranges[7] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                     |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(8, 13);
        $control = new CompressedIntSet();
        $control->ranges[8] = 13;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                        |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(9, 14);
        $control = new CompressedIntSet();
        $control->ranges[8] = 14;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                           |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(10, 15);
        $control = new CompressedIntSet();
        $control->ranges[8] = 15;
        self::assertTrue($c->equals($control));


        //----------------------------------------------------------------------------------

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                              |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(11, 16);
        $control = new CompressedIntSet();
        $control->ranges[8] = 16;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                                 |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(12, 17);
        $control = new CompressedIntSet();
        $control->ranges[8] = 17;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                                    |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(13, 18);
        $control = new CompressedIntSet();
        $control->ranges[8] = 18;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                                       |_________________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(14, 19);
        $control = new CompressedIntSet();
        $control->ranges[8] = 12;
        $control->ranges[14] = 19;
        self::assertTrue($c->equals($control));
    }

    function test_addRange_same_size(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //|______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(1, 5);
        $control = new CompressedIntSet();
        $control->ranges[1] = 5;
        $control->ranges[8] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //   |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(2, 6);
        $control = new CompressedIntSet();
        $control->ranges[2] = 6;
        $control->ranges[8] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //      |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(3, 7);
        $control = new CompressedIntSet();
        $control->ranges[3] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //         |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(4, 8);
        $control = new CompressedIntSet();
        $control->ranges[4] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //            |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(5, 9);
        $control = new CompressedIntSet();
        $control->ranges[5] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //               |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(6, 10);
        $control = new CompressedIntSet();
        $control->ranges[6] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                  |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(7, 11);
        $control = new CompressedIntSet();
        $control->ranges[7] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                     |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(8, 12);
        $control = new CompressedIntSet();
        $control->ranges[8] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                        |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(9, 13);
        $control = new CompressedIntSet();
        $control->ranges[8] = 13;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                           |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(10, 14);
        $control = new CompressedIntSet();
        $control->ranges[8] = 14;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                              |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(11, 15);
        $control = new CompressedIntSet();
        $control->ranges[8] = 15;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                                 |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(12, 16);
        $control = new CompressedIntSet();
        $control->ranges[8] = 16;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                                    |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(13, 17);
        $control = new CompressedIntSet();
        $control->ranges[8] = 17;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                                       |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(14, 18);
        $control = new CompressedIntSet();
        $control->ranges[8] = 12;
        $control->ranges[14] = 18;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |______________|
        //                                          |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 12;
        $c->addRange(15, 19);
        $control = new CompressedIntSet();
        $control->ranges[8] = 12;
        $control->ranges[15] = 19;
        self::assertTrue($c->equals($control));
    }

    function test_addRange_1_unit_smaller(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //|___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(1, 4);
        $control = new CompressedIntSet();
        $control->ranges[1] = 4;
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //   |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(2, 5);
        $control = new CompressedIntSet();
        $control->ranges[2] = 5;
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //      |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(3, 6);
        $control = new CompressedIntSet();
        $control->ranges[3] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //         |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(4, 7);
        $control = new CompressedIntSet();
        $control->ranges[4] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //            |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(5, 8);
        $control = new CompressedIntSet();
        $control->ranges[5] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //               |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(6, 9);
        $control = new CompressedIntSet();
        $control->ranges[6] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                  |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(7, 10);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                     |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(8, 11);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                        |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(9, 12);
        $control = new CompressedIntSet();
        $control->ranges[7] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                           |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(10, 13);
        $control = new CompressedIntSet();
        $control->ranges[7] = 13;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                              |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(11, 14);
        $control = new CompressedIntSet();
        $control->ranges[7] = 14;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                 |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(12, 15);
        $control = new CompressedIntSet();
        $control->ranges[7] = 15;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                    |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(13, 16);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        $control->ranges[13] = 16;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                       |___________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(14, 17);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        $control->ranges[14] = 17;
        self::assertTrue($c->equals($control));
    }

    function test_addRange_2_units_smaller(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //   |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(2, 4);
        $control = new CompressedIntSet();
        $control->ranges[2] = 4;
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //      |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(3, 5);
        $control = new CompressedIntSet();
        $control->ranges[3] = 5;
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //         |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(4, 6);
        $control = new CompressedIntSet();
        $control->ranges[4] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //            |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(5, 7);
        $control = new CompressedIntSet();
        $control->ranges[5] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //               |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(6, 8);
        $control = new CompressedIntSet();
        $control->ranges[6] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                  |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(7, 9);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                     |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(8, 10);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                        |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(9, 11);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                           |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(10, 12);
        $control = new CompressedIntSet();
        $control->ranges[7] = 12;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                              |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(11, 13);
        $control = new CompressedIntSet();
        $control->ranges[7] = 13;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                 |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(12, 14);
        $control = new CompressedIntSet();
        $control->ranges[7] = 14;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                    |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(13, 15);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        $control->ranges[13] = 15;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                       |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->addRange(14, 16);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        $control->ranges[14] = 16;
        self::assertTrue($c->equals($control));
    }

    function test_addRange_of_1_to_existing_range_of_3(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //   |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->addRange(2, 2);
        $control = new CompressedIntSet();
        $control->ranges[2] = 2;
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //      |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->addRange(3, 3);
        $control = new CompressedIntSet();
        $control->ranges[3] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //         |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->addRange(4, 4);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //            |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->addRange(5, 5);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //               |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->addRange(6, 6);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //                  |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->addRange(7, 7);
        $control = new CompressedIntSet();
        $control->ranges[4] = 7;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //                     |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->addRange(8, 8);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        $control->ranges[8] = 8;
        self::assertTrue($c->equals($control));
    }

    function test_addRange_of_3_to_existing_range_of_1(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //   |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(2, 4);
        $control = new CompressedIntSet();
        $control->ranges[2] = 4;
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //      |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(3, 5);
        $control = new CompressedIntSet();
        $control->ranges[3] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //         |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(4, 6);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //            |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(5, 7);
        $control = new CompressedIntSet();
        $control->ranges[5] = 7;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //               |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(6, 8);
        $control = new CompressedIntSet();
        $control->ranges[6] = 8;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //                  |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(7, 9);
        $control = new CompressedIntSet();
        $control->ranges[6] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //                     |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(8, 10);
        $control = new CompressedIntSet();
        $control->ranges[6] = 6;
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));
    }

    function test_addRange_of_1_to_existing_range_of_1(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //         |__|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(4, 4);
        $control = new CompressedIntSet();
        $control->ranges[4] = 4;
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //            |__|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(5, 5);
        $control = new CompressedIntSet();
        $control->ranges[5] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //               |__|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(6, 6);
        $control = new CompressedIntSet();
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //                  |__|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(7, 7);
        $control = new CompressedIntSet();
        $control->ranges[6] = 7;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //                     |__|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->addRange(8, 8);
        $control = new CompressedIntSet();
        $control->ranges[6] = 6;
        $control->ranges[8] = 8;
        self::assertTrue($c->equals($control));
    }

    //[][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][][]

    function test_removeRange_of_1_from_existing_range_of_1(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |__|
        //   |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 4;
        $c->removeRange(2, 2);
        $control = new CompressedIntSet();
        $control->ranges[4] = 4;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |__|
        //      |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 4;
        $c->removeRange(3, 3);
        $control = new CompressedIntSet();
        $control->ranges[4] = 4;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |__|
        //         |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 4;
        $c->removeRange(4, 4);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |__|
        //            |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 4;
        $c->removeRange(5, 5);
        $control = new CompressedIntSet();
        $control->ranges[4] = 4;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |__|
        //               |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 4;
        $c->removeRange(6, 6);
        $control = new CompressedIntSet();
        $control->ranges[4] = 4;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_of_3_from_existing_range_of_1(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //   |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->removeRange(2, 4);
        $control = new CompressedIntSet();
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //      |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->removeRange(3, 5);
        $control = new CompressedIntSet();
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //         |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->removeRange(4, 6);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //            |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->removeRange(5, 7);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //               |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->removeRange(6, 8);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //                  |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->removeRange(7, 9);
        $control = new CompressedIntSet();
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //               |__|
        //                     |________|
        $c = new CompressedIntSet();
        $c->ranges[6] = 6;
        $c->removeRange(8, 10);
        $control = new CompressedIntSet();
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_of_1_from_existing_range_of_3(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //   |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->removeRange(2, 2);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //      |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->removeRange(3, 3);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //         |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->removeRange(4, 4);
        $control = new CompressedIntSet();
        $control->ranges[5] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //            |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->removeRange(5, 5);
        $control = new CompressedIntSet();
        $control->ranges[4] = 4;
        $control->ranges[6] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //               |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->removeRange(6, 6);
        $control = new CompressedIntSet();
        $control->ranges[4] = 5;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //                  |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->removeRange(7, 7);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //         |________|
        //                     |__|
        $c = new CompressedIntSet();
        $c->ranges[4] = 6;
        $c->removeRange(8, 8);
        $control = new CompressedIntSet();
        $control->ranges[4] = 6;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_split(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //   |_______________________|
        //      |________|
        $c = new CompressedIntSet();
        $c->ranges[2] = 9;
        $c->removeRange(3, 5);
        $control = new CompressedIntSet();
        $control->ranges[2] = 2;
        $control->ranges[6] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //   |_______________________|
        //         |________|
        $c = new CompressedIntSet();
        $c->ranges[2] = 9;
        $c->removeRange(4, 6);
        $control = new CompressedIntSet();
        $control->ranges[2] = 3;
        $control->ranges[7] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //   |_______________________|
        //            |________|
        $c = new CompressedIntSet();
        $c->ranges[2] = 9;
        $c->removeRange(5, 7);
        $control = new CompressedIntSet();
        $control->ranges[2] = 4;
        $control->ranges[8] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //   |_______________________|
        //               |________|
        $c = new CompressedIntSet();
        $c->ranges[2] = 9;
        $c->removeRange(6, 8);
        $control = new CompressedIntSet();
        $control->ranges[2] = 5;
        $control->ranges[9] = 9;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_2_units_smaller(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //   |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(2, 4);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //      |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(3, 5);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //         |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(4, 6);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //            |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(5, 7);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //               |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(6, 8);
        $control = new CompressedIntSet();
        $control->ranges[9] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                  |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(7, 9);
        $control = new CompressedIntSet();
        $control->ranges[10] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                     |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(8, 10);
        $control = new CompressedIntSet();
        $control->ranges[7] = 7;
        $control->ranges[11] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                        |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(9, 11);
        $control = new CompressedIntSet();
        $control->ranges[7] = 8;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                           |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(10, 12);
        $control = new CompressedIntSet();
        $control->ranges[7] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                              |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(11, 13);
        $control = new CompressedIntSet();
        $control->ranges[7] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                 |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(12, 14);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                    |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(13, 15);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                  |______________|
        //                                       |________|
        $c = new CompressedIntSet();
        $c->ranges[7] = 11;
        $c->removeRange(14, 16);
        $control = new CompressedIntSet();
        $control->ranges[7] = 11;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_1_unit_smaller(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //      |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(3, 5);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //         |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(4, 6);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //            |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(5, 7);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //               |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(6, 8);
        $control = new CompressedIntSet();
        $control->ranges[9] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                  |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(7, 9);
        $control = new CompressedIntSet();
        $control->ranges[10] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                     |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(8, 10);
        $control = new CompressedIntSet();
        $control->ranges[11] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                        |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(9, 11);
        $control = new CompressedIntSet();
        $control->ranges[8] = 8;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                           |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(10, 12);
        $control = new CompressedIntSet();
        $control->ranges[8] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                              |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(11, 13);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                                 |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(12, 14);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                                    |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(13, 15);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                                       |________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(14, 16);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_same_size(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //   |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(2, 5);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //      |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(3, 6);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //         |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(4, 7);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //            |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(5, 8);
        $control = new CompressedIntSet();
        $control->ranges[9] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //               |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(6, 9);
        $control = new CompressedIntSet();
        $control->ranges[10] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                  |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(7, 10);
        $control = new CompressedIntSet();
        $control->ranges[11] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                     |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(8, 11);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                        |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(9, 12);
        $control = new CompressedIntSet();
        $control->ranges[8] = 8;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                           |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(10, 13);
        $control = new CompressedIntSet();
        $control->ranges[8] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                              |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(11, 14);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                                 |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(12, 15);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                                    |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(13, 16);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |___________|
        //                                       |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 11;
        $c->removeRange(14, 17);
        $control = new CompressedIntSet();
        $control->ranges[8] = 11;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_1_unit_bigger(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //   |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(2, 5);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //      |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(3, 6);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //         |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(4, 7);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //            |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(5, 8);
        $control = new CompressedIntSet();
        $control->ranges[9] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //               |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(6, 9);
        $control = new CompressedIntSet();
        $control->ranges[10] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                  |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(7, 10);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                     |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(8, 11);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                        |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(9, 12);
        $control = new CompressedIntSet();
        $control->ranges[8] = 8;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                           |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(10, 13);
        $control = new CompressedIntSet();
        $control->ranges[8] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                              |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(11, 14);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                                 |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(12, 15);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                                    |___________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(13, 16);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));
    }

    function test_removeRange_2_units_bigger(){
        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //|______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(1, 5);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //   |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(2, 6);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //      |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(3, 7);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //         |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(4, 8);
        $control = new CompressedIntSet();
        $control->ranges[9] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //            |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(5, 9);
        $control = new CompressedIntSet();
        $control->ranges[10] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //               |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(6, 10);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                  |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(7, 11);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                     |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(8, 12);
        $control = new CompressedIntSet();
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                        |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(9, 13);
        $control = new CompressedIntSet();
        $control->ranges[8] = 8;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                           |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(10, 14);
        $control = new CompressedIntSet();
        $control->ranges[8] = 9;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                              |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(11, 15);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                                 |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(12, 16);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));

        // 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
        //                     |________|
        //                                    |______________|
        $c = new CompressedIntSet();
        $c->ranges[8] = 10;
        $c->removeRange(13, 17);
        $control = new CompressedIntSet();
        $control->ranges[8] = 10;
        self::assertTrue($c->equals($control));
    }
}
