<?php


namespace App\Helpers;


class StringGenericHelper
{
    /**
     * Generates 3 random number between 0 and 255.
     * These numbers will the RGB value respectively
     *
     * @return array
     */
    public static function generateRandomRgb(): array
    {
        $rgbColor = [];

        //loop 3 times to generate random numbers for R G B section
        foreach(array('r', 'g', 'b') as $color){
            //Generate a random number between 0 and 255.
            $rgbColor[$color] = mt_rand(0, 255);
        }

        return $rgbColor;
    }

    /**
     * Generates a RGB string
     *
     * @return string
     */
    public static function generateRgbString(): string
    {
        // generate RGB numbers
        $rgb = self::generateRandomRgb();

        // tie the numbers
        $imploded = implode(', ', $rgb);

        // return the rgb string
        return "rgb({$imploded})";
    }

    /**
     * Generates a RGBA string
     *
     * @param int $opacity
     *
     * @return string
     */
    public static function generateRgbaString(int $opacity)
    {
        // generate RGB numbers
        $rgb = self::generateRandomRgb();

        // tie the numbers together
        $imploded = implode(', ', $rgb);

        // return the rgba string
        return "rgba({$imploded}, {$opacity})";
    }
}
