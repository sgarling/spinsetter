<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
if (!function_exists('imageCrop'))
{
    /* 	Esta funcionsota me la mando Gerardo, corta las imagenes en el tamaño especificado, y las guada 
		con formato jpg por defecto en $sImageDest.
		Le saque algunas cosas de las cuales no iba a hacer uso, esta fnc. no redimensiona, "cropea"
		Prototipo: imageCrop(string filename , string destFilename , int width , int height,bool conWatermark )    */		
	function imageCrop($sImagePath, $sImageDest, $iWidth=112, $iHeight=87 , $conWater = false) {

		  //obtener el tamaño de la imagen

		  list($iImageWidth, $iImageHeight, $iImgTipo)=getimagesize($sImagePath);

		  $imageInfo = GetImageSize($sImagePath);
			
		  //if(!ini_set('memory_limit', "8M"))
			//return false;//"No se pudo reservar memoria";*/

		  // Crear la imagen:

		  if($iImgTipo==1)

				$iFondo = ImageCreateFromGif($sImagePath);

		  elseif($iImgTipo==2)

				$iFondo = ImageCreateFromJpeg($sImagePath);

		  elseif($iImgTipo==3)

				$iFondo = ImageCreateFromPng($sImagePath);

		  elseif($iImgTipo==6)

				$iFondo = ImageCreateFromBmp($sImagePath);

		  elseif($iImgTipo==15)

				$iFondo = ImageCreateFromWbmp($sImagePath);

		  else{

				return false;//"Tipo de imagen no soportado. Tipo:".$iImgTipo;

		  }

		  //Radio de corte (porque el crop no es cuadrado)

		  $iCutRatio = $iWidth / $iHeight;

		  $iWidthRatio = $iHeight / $iWidth;

		  

		  //Ancho maximo, aca se calcula si la imagen es mas ancha que la proporcion necesaria o no

		  //$iMaxWidth = round($iCutRatio * $iImageWidth);  840 "465,6"??y q onda un floor ??

		  $iMaxWidth = round($iCutRatio * $iImageHeight);

		  //Alto maximo, por si la imagen es mas alta que lo necesario

		  $iMaxHeight = round($iWidthRatio * $iImageWidth); //583,3333333333333333331  y que onda un floor ??
	 
		  // Crear una imagen en blanco

		  $iCanvas = @ImageCreateTrueColor($iWidth, $iHeight);


		  // Calcular si sobrepasa ancho maximo

		  if($iImageWidth > $iMaxWidth){

				//cuanto hay que cortar de la imagen?

				$iXCut = round(($iImageWidth - $iMaxWidth) / 2);

				//Copiar el cuadrado que necesitamos de la imagen origen hacia en thumb

				//imagecopyresampled(img_dst, img_org, Xdst, Ydst, Xorg, Yorg, ancho_dst, alto_dst, ancho_org, alto_org )

				ImageCopyResampled($iCanvas, $iFondo, 0, 0, $iXCut, 0, $iWidth, $iHeight, $iMaxWidth, $iImageHeight);

		  }

		  elseif($iImageWidth < $iMaxWidth){

				//cuanto hay que cortar de la imagen?

				$iYCut = round(($iImageHeight - $iMaxHeight) / 2);

				//Copiar el cuadrado que necesitamos de la imagen origen hacia en thumb

				ImageCopyResampled($iCanvas, $iFondo, 0, 0, 0, $iYCut, $iWidth, $iHeight, $iImageWidth, $iMaxHeight);

		  }
		  else{

				//la imagen tiene el radio perfecto
				//Copiar el cuadrado que necesitamos de la imagen origen hacia en thumb

				ImageCopyResampled($iCanvas, $iFondo, 0, 0, 0, 0, $iWidth, $iHeight, $iImageWidth, $iImageHeight);

		  }

		  //Ahora le agregamos los dos watermarks:
		  if ( $conWater )
			{
				$watermark1 = imagecreatefrompng('img/watermark1.png');
				$watermark2 = imagecreatefrompng('img/watermark2.png');

				imagecopy ( $iCanvas , $watermark1 , 0 , 0 , 0 , 0 , imagesx($watermark1) , imagesy($watermark1) );
				imagecopy ( $iCanvas , $watermark2 , 570 , 0 , 0 , 0 , imagesx($watermark2) , imagesy($watermark2) );
			}
		  

		  // Ya tenemos $iCanvas, que es la imagen ya procesada
			
		  imagejpeg($iCanvas, $sImageDest, 80);
		  
			if(!imagedestroy($iCanvas))
				return false;//No se pudo liberar memoria
			if(!imagedestroy($iFondo))
				return false;//No se pudo liberar memoria
			
			if(file_exists($sImageDest))
				return true;//$sImageDest;
		
			else
				return false;

	}
}
 
