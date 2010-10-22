<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$idir = "images/original/";   // Path To Images Directory
$tdir = "images/thumbs/";   // Path To Thumbnails Directory
$mdir = "images/normal/";   // Path To Normal size Directory


function resize_product_pic($product_id = "0") {
$twidth = "150";   // Maximum Width For Thumbnail Images
$theight = "150";   // Maximum Height For Thumbnail Images
$mwidth = "500";   // Maximum Width For Main Images
$mheight = "500";   // Maximum Height For Main Images
global $idir, $tdir, $mdir;

// make sure the image folders exist
 if (!is_dir($idir)) mkdir($idir, 0777, true); 
 if (!is_dir($tdir)) mkdir($tdir, 0777, true); 
 if (!is_dir($mdir)) mkdir($mdir, 0777, true); 
 echo "resizing: " . "$idir" . $product_id . ".jpg" . "<br>";
 
	  $simg = imagecreatefromjpeg("$idir" . $product_id . ".jpg");   // Make A New Temporary Image To Create The Thumbanil From
      $currwidth = imagesx($simg);   // Current Image Width
      $currheight = imagesy($simg);   // Current Image Height
      if ($currheight > $currwidth) {   // If Height Is Greater Than Width
         $zoom = $mheight / $currheight;   // Length Ratio For Width
         $newheight = $mheight;   // Height Is Equal To Max Height
         $newwidth = round($currwidth * $zoom,0);   // Creates The New Width
      } else {    // Otherwise, Assume Width Is Greater Than Height (Will Produce Same Result If Width Is Equal To Height)
        $zoom = $mwidth / $currwidth;   // Length Ratio For Height
        $newwidth = $mwidth;   // Width Is Equal To Max Width
        $newheight = round($currheight * $zoom,0);   // Creates The New Height
      }
      $dimg = imagecreatetruecolor($newwidth, $newheight);   // Make New Image For Thumbnail
      imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $newwidth, $newheight, $currwidth, $currheight);   // Copy Resized Image To The New Image (So We Can Save It)
      imagejpeg($dimg, "$mdir" . $product_id . ".jpg");   // Saving The main (or normal) Image
      imagedestroy($dimg);   // Destroying The Other Temporary Image
 echo "saving: " . "$mdir" . $product_id . ".jpg" . " " . $newwidth . "x". $newheight . "<br>";
	
	// Make the thumbnail image
      if ($currheight > $currwidth) {   // If Height Is Greater Than Width
         $zoom = $theight / $currheight;   // Length Ratio For Width
         $newheight = $theight;   // Height Is Equal To Max Height
         $newwidth = round($currwidth * $zoom,0);   // Creates The New Width
      } else {    // Otherwise, Assume Width Is Greater Than Height (Will Produce Same Result If Width Is Equal To Height)
        $zoom = $twidth / $currwidth;   // Length Ratio For Height
        $newwidth = $twidth;   // Width Is Equal To Max Width
        $newheight = round($currheight * $zoom,0);   // Creates The New Height
      }
      $dimg = imagecreatetruecolor($newwidth, $newheight);   // Make New Image For Thumbnail
      imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $newwidth, $newheight, $currwidth, $currheight);   // Copy Resized Image To The New Image (So We Can Save It)
      imagejpeg($dimg, "$tdir" . $product_id . ".jpg");   // Saving The Image
      imagedestroy($simg);   // Destroying The Temporary Image
      imagedestroy($dimg);   // Destroying The Other Temporary Image
	  return (1);
}


if (isset($_GET['product_id'])) resize_product_pic($_GET['product_id']);
 
?>
