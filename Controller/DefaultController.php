<?php

namespace Praktika\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Praktika\TestBundle\Entity\Points;
use Praktika\TestBundle\Entity\APoints;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\DoctrineBehaviors\ORM\Geocodable\Type\Point;
use Knp\DoctrineBehaviors\ORM\Geocodable\Type\APoint;
use Praktika\TestBundle\Form\Extension\FormType;
use Praktika\TestBundle\Form\Extension\FormAType;
use TCPDF;
use Praktika\TestBundle\Visual\Visual;

class DefaultController extends Controller
{
	public function emptyString ($string) {
		if ($string=="")
			return true;
		else
			return false;
	}

    public function indexAction()
    {
        return $this->render('PraktikaTestBundle:Default:index.html.twig');
    }
	
	public function editpoint1Action (Request $request) {
		$messages = array(); 
		$id = -1;
		if (isset($_GET['id'])) {
			$id = intval($_GET['id']);
			$em = $this->getDoctrine()->getEntityManager();
			$product = $em->getRepository('PraktikaTestBundle:Points')->find($id);
			$data['latitude'] = $product->getPoint()->getLatitude();
			$data['longitude'] = $product->getPoint()->getLongitude();
			$data['height'] = $product->getPoint()->getHeight();
			$data['title'] = $product->getTitle();
		} else {
			$message = array('status'=>1, 'text' => 'Ошибка передачи данных о точке');
			$messages[] = $message;	
		}
		
		$errorEdit = false; // all right
		if ($request->getMethod() == 'POST') {
			if ($this->emptyString($_POST['latitude']) || $this->emptyString($_POST['longitude']) || $this->emptyString($_POST['height']) || $this->emptyString($_POST['title'])) {
				$errorAdd = true;
				$message = array('status'=>1, 'text' => 'Заполнены не все поля');
				$messages[] = $message;				
			}
			else {
				$repository = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points');
				$pp = new Point();
				$result = $pp->setFromLLH($_POST['latitude'], $_POST['longitude'], $_POST['height']);
				if ($result == 0) {
					// проверка наличия такой точки
					$point = $repository->findOneBy(array('point' => $pp));
					if ($point && $point->getId()!=$id) {
						$message = array('status'=>1, 'text' => 'Географический объект с такими координатами уже есть в базе данных');
						$messages[] = $message;		
						$errorEdit = true;
					}
					else {
						$updPoint = $repository->find($id);
						$updPoint->setPoint($pp);
						$updPoint->setTitle($_POST['title']);
						$em->flush();
						return $this->redirect($this->generateUrl('praktika_test_viewp1')."?update");
					}				
				}
				else {
					$errorEdit = true;
					// собираем все полученные ошибки
					if ($result & 1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат высоты: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<2) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: должно быть число');
						$messages[] = $message;
					}	
					if ($result & 1<<3) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: число должно быть географической координатой');
						$messages[] = $message;
					}	
					if ($result & 1<<4) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: число должно быть географической координатой');
						$messages[] = $message;
					}					
				}
			}
		}
	
		// добавляем названия
		$formArray = array ('label_longitude'=>'Долгота',
							'label_latitude'=>'Широта',
							'label_height'=>'Высота',
							'label_title'=>'Название');
		
		// подготавливаем массив в форму
		foreach (array ('longitude', 'latitude', 'height', 'title') as $v)
			if (isset($_POST[$v]))
				$formArray[$v]=$_POST[$v];
			else if(isset($data[$v]))
				$formArray[$v]=$data[$v];
			
		
		$form = $this->CreateForm(new FormType(), $formArray);
        return $this->render('PraktikaTestBundle:Default:editpoint1.html.twig', array ('form'=>$form->createView(), 'messages'=>$messages, 'id'=>$id));
	}
############################################## VIEW 1 #########################################	
	    public function viewp1Action(Request $request)
    {
		// message to view
		// status - 0 good, 1 bad
		// text - text of message
		// example
		// $messages[0]['status'] = 0, $messages[0]['text'] = "Delete completed"
		$messages = array(); 
		if ($request->getMethod() =='GET') {
			if (isset($_GET['del'])) {
				$id = intval($_GET['del']);
				$em = $this->getDoctrine()->getEntityManager();
				$product = $em->getRepository('PraktikaTestBundle:Points')->find($id);
				
				if (!$product) {
					$message = array('status'=>1, 'text' => 'Нет такой точки');
					$messages[] = $message;
				}
				else {
					$em->remove($product);
					$em->flush();
					$message = array('status'=>0, 'text' => 'Удаление успешно выполнено');
					$messages[] = $message;
				}
			}
			if (isset($_GET['update'])) {
				$message = array('status'=>0, 'text' => 'Изменение успешно выполнено');
				$messages[] = $message;				
			}
		}
		$errorAdd = false; // all right
		if ($request->getMethod() == 'POST') {
			if ($this->emptyString($_POST['latitude']) || $this->emptyString($_POST['longitude']) || $this->emptyString($_POST['height']) || $this->emptyString($_POST['title'])) {
				$errorAdd = true;
				$message = array('status'=>1, 'text' => 'Заполнены не все поля');
				$messages[] = $message;				
			}
			else {
				$newp = new Points();
				$newp->setTitle($_POST['title']);
				$pp = new Point();
				$result = $pp->setFromLLH($_POST['latitude'], $_POST['longitude'], $_POST['height']);
				if ($result == 0) {
					// проверка наличия такой точки
					$repository = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points');
					$point = $repository->findOneBy(array('point' => $pp));
					if ($point) {
						$message = array('status'=>1, 'text' => 'Географический объект с такими координатами уже есть в базе данных');
						$messages[] = $message;		
						$errorAdd = true;
					}
					else {
						$newp->setPoint($pp);
						$em = $this->getDoctrine()->getEntityManager();
						$em->persist($newp);
						$em->flush();
						$message = array('status'=>0, 'text' => 'Добавление успешно выполнено');
						$messages[] = $message;	
					}				
				}
				else {
					$errorAdd = true;
					// собираем все полученные ошибки
					if ($result & 1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат высоты: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<2) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: должно быть число');
						$messages[] = $message;
					}	
					if ($result & 1<<3) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: число должно быть географической координатой');
						$messages[] = $message;
					}	
					if ($result & 1<<4) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: число должно быть географической координатой');
						$messages[] = $message;
					}					
				}
			}
		}
		
		
		$pts = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points')->findAll();
	
		// добавляем названия
		$formArray = array ('label_longitude'=>'Долгота',
							'label_latitude'=>'Широта',
							'label_height'=>'Высота',
							'label_title'=>'Название');
		
		if ($errorAdd) {
			// подготавливаем массив в форму
			foreach (array ('longitude', 'latitude', 'height', 'title') as $v)
				if (isset($_POST[$v]))
					$formArray[$v]=$_POST[$v];
		}
			
		
		$form = $this->CreateForm(new FormType(), $formArray);
        return $this->render('PraktikaTestBundle:Default:viewp1.html.twig', array ('points'=>$pts, 'form'=>$form->createView(), 'messages'=>$messages));
    }
############################################## EDIT 2 #########################################

	public function editpoint2Action (Request $request) {
		$messages = array(); 
		$id = -1;
		if (isset($_GET['id'])) {
			$id = intval($_GET['id']);
			$em = $this->getDoctrine()->getEntityManager();
			$product = $em->getRepository('PraktikaTestBundle:APoints')->find($id);
			$data['latitude'] = $product->getPoint()->getBase()->getLatitude();
			$data['longitude'] = $product->getPoint()->getBase()->getLongitude();
			$data['height'] = $product->getPoint()->getBase()->getHeight();
			$data['azimuth'] = $product->getPoint()->getAzimuth();
			$data['distance'] = $product->getPoint()->getDistance();
			$data['title'] = $product->getTitle();
		} else {
			$message = array('status'=>1, 'text' => 'Ошибка передачи данных о точке');
			$messages[] = $message;	
		}
		
		$errorEdit = false; // all right
		if ($request->getMethod() == 'POST') {
			if ($this->emptyString($_POST['latitude']) || $this->emptyString($_POST['longitude']) || $this->emptyString($_POST['height']) || $this->emptyString($_POST['title'])
			|| $this->emptyString($_POST['azimuth']) || $this->emptyString($_POST['distance'])) {
				$errorEdit = true;
				$message = array('status'=>1, 'text' => 'Заполнены не все поля');
				$messages[] = $message;				
			}
			else {
				$repository = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints');
				$pp = new APoint();
				$result = $pp->setFromLLHAD($_POST['latitude'], $_POST['longitude'], $_POST['height'], $_POST['azimuth'], $_POST['distance']);
				if ($result == 0) {
					// проверка наличия такой точки
					$point = $repository->findOneBy(array('point' => $pp));
					if ($point && $point->getId()!=$id) {
						$message = array('status'=>1, 'text' => 'Географический объект с такими координатами уже есть в базе данных');
						$messages[] = $message;		
						$errorEdit = true;
					}
					else {
						$updPoint = $repository->find($id);
						$updPoint->setPoint($pp);
						$updPoint->setTitle($_POST['title']);
						$em->flush();
						return $this->redirect($this->generateUrl('praktika_test_viewp2')."?update");
					}				
				}
				else {
					$errorEdit = true;
					// собираем все полученные ошибки
					if ($result & 1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат высоты: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<2) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: должно быть число');
						$messages[] = $message;
					}	
					if ($result & 1<<3) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: число должно быть географической координатой');
						$messages[] = $message;
					}	
					if ($result & 1<<4) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: число должно быть географической координатой');
						$messages[] = $message;
					}		
					if ($result & 1<<5) {
						$message = array('status'=>1, 'text' => 'Неправильный формат расстояния: должно быть число');
						$messages[] = $message;
					}		
					if ($result & 1<<6) {
						$message = array('status'=>1, 'text' => 'Неправильный формат азимута: должно быть число');
						$messages[] = $message;
					}	
					if ($result & 1<<7) {
						$message = array('status'=>1, 'text' => 'Неправильный формат азимута: должен быть угол в градусах от 0 до 360');
						$messages[] = $message;
					}						
				}
			}
		}
	
		// добавляем названия
		$formArray = array ('label_longitude'=>'Долгота',
							'label_latitude'=>'Широта',
							'label_height'=>'Высота',
							'label_title'=>'Название',
							'label_azimuth'=>'Азимут',
							'label_distance'=>'Расстояние');
		
		// подготавливаем массив в форму
		foreach (array ('longitude', 'latitude', 'height', 'azimuth', 'distance', 'title') as $v)
			if (isset($_POST[$v]))
				$formArray[$v]=$_POST[$v];
			else if(isset($data[$v]))
				$formArray[$v]=$data[$v];
			
		
		$form = $this->CreateForm(new FormAType(), $formArray);
        return $this->render('PraktikaTestBundle:Default:editpoint2.html.twig', array ('form'=>$form->createView(), 'messages'=>$messages, 'id'=>$id));
	}
	
############################################## VIEW 2 #########################################
	    public function viewp2Action(Request $request)
    {
		$messages = array();
		
		if ($request->getMethod() =='GET') {
			if (isset($_GET['del'])) {
				$id = intval($_GET['del']);
				$em = $this->getDoctrine()->getEntityManager();
				$product = $em->getRepository('PraktikaTestBundle:APoints')->find($id);
				
				if (!$product) {
					$message = array('status'=>1, 'text' => 'Нет такой точки');
					$messages[] = $message;
				}
				else {
					$em->remove($product);
					$em->flush();
					$message = array('status'=>0, 'text' => 'Удаление успешно выполнено');
					$messages[] = $message;
				}
			}
			if (isset($_GET['update'])) {
				$message = array('status'=>0, 'text' => 'Изменение успешно выполнено');
				$messages[] = $message;				
			}
		}
		$errorAdd = false; // all right
		
		if ($request->getMethod() == 'POST') {
			if ($this->emptyString($_POST['latitude']) || $this->emptyString($_POST['longitude']) || $this->emptyString($_POST['height']) || $this->emptyString($_POST['title']) 
			|| $this->emptyString($_POST['azimuth']) || $this->emptyString($_POST['distance'])) {
				$errorAdd = true;
				$message = array('status'=>1, 'text' => 'Заполнены не все поля');
				$messages[] = $message;				
			}
			else {
				$newp = new APoints();
				$newp->setTitle($_POST['title']);
				$pp = new APoint();
				$result = $pp->setFromLLHAD($_POST['latitude'], $_POST['longitude'], $_POST['height'], $_POST['azimuth'], $_POST['distance']);
				if ($result==0) {
					// проверка наличия такой точки
					$repository = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints');
					$point = $repository->findOneBy(array('point' => $pp));
					if ($point) {
						$message = array('status'=>1, 'text' => 'Географический объект с такими координатами уже есть в базе данных');
						$messages[] = $message;		
						$errorAdd = true;
					}
					else {
						$newp->setPoint($pp);
						$em = $this->getDoctrine()->getEntityManager();
						$em->persist($newp);
						$em->flush();
						$message = array('status'=>0, 'text' => 'Добавление успешно выполнено');
						$messages[] = $message;	
					}
				}
				else {
					$errorAdd = true;
					// собираем все полученные ошибки
					if ($result & 1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат высоты: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<1) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: должно быть число');
						$messages[] = $message;
					}
					if ($result & 1<<2) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: должно быть число');
						$messages[] = $message;
					}	
					if ($result & 1<<3) {
						$message = array('status'=>1, 'text' => 'Неправильный формат долготы: число должно быть географической координатой');
						$messages[] = $message;
					}	
					if ($result & 1<<4) {
						$message = array('status'=>1, 'text' => 'Неправильный формат широты: число должно быть географической координатой');
						$messages[] = $message;
					}			
					if ($result & 1<<5) {
						$message = array('status'=>1, 'text' => 'Неправильный формат расстояния: должно быть число');
						$messages[] = $message;
					}		
					if ($result & 1<<6) {
						$message = array('status'=>1, 'text' => 'Неправильный формат азимута: должно быть число');
						$messages[] = $message;
					}	
					if ($result & 1<<7) {
						$message = array('status'=>1, 'text' => 'Неправильный формат азимута: должен быть угол в градусах от 0 до 360');
						$messages[] = $message;
					}					
				}
			}
		}	
		$pts = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints')->findAll();
		
		// добавляем названия
		$formArray = array ('label_longitude'=>'Долгота',
							'label_latitude'=>'Широта',
							'label_height'=>'Высота',
							'label_title'=>'Название',
							'label_azimuth'=>'Азимут',
							'label_distance'=>'Расстояние');
		
		if ($errorAdd) {
			// подготавливаем массив в форму
			foreach (array ('longitude', 'latitude', 'height', 'title', 'distance', 'azimuth') as $v)
				if (isset($_POST[$v]))
					$formArray[$v]=$_POST[$v];
		}
			
		
		$form = $this->CreateForm(new FormAType(), $formArray);
		
        return $this->render('PraktikaTestBundle:Default:viewp2.html.twig', array ('form'=>$form->createView(), 'points'=>$pts, 'messages'=>$messages));
    }
	
	
################################### CALC DISTANCE ###################	
	    public function distAction(Request $request)
    {
		$dist = null;
		if ($request->getMethod() == 'POST') {
			$pt1 = $_POST['point1'];
			$pt2 = $_POST['point2'];
			$pnt1 = new Point();
			$pnt2 = new Point();
			if ($pt1[0] == 'a') {
				preg_match('#a([0-9]{1,})#im', $pt1, $arr);
				$tmp = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints')->find($arr[1]);
				$pnt1->setFromAPoint($tmp->getPoint());
			}
			else {
				$tmp = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points')->find($pt1);
				$pnt1=$tmp->getPoint();
			}
			if ($pt2[0] == 'a') {
				preg_match('#a([0-9]{1,})#im', $pt2, $arr);
				$tmp = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints')->find($arr[1]);
				$pnt2->setFromAPoint($tmp->getPoint());
			}
			else {
				$tmp = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points')->find($pt2);
				$pnt2=$tmp->getPoint();
			}		

			$dist = $pnt1->getDistanceToPoint($pnt2);
		}
		
		$pts = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points')->findAll();
		$apts = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints')->findAll();
        return $this->render('PraktikaTestBundle:Default:dist.html.twig', array ('dist'=>$dist, 'pts'=>$pts, 'apts'=>$apts));
    }	

################################### GOOGLE MAPS ###################		
	    public function googlemapsAction(Request $request)
    {
		$addClick = false;
		if (isset($_GET['addclick'])) {
			$addClick = true;
			
			if (isset($_POST['title'])) {
				$lat = $_POST['lat'];
				$long = $_POST['long'];
				$height = 0;
				$title = $_POST['title'];
				$newp = new Points();
				$newp->setTitle($title);
				$pp = new Point();
				$result = $pp->setFromLLH($lat, $long, $height);
				$repository = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points');
				$newp->setPoint($pp);
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($newp);
				$em->flush();
				echo "Добавление точки <b>$title</b> успешно выполнено";
				exit;
			}
		}
		$pts = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points')->findAll();
		return $this->render('PraktikaTestBundle:Default:googlemaps.html.twig', array ('pts'=>$pts, 'addClick'=>$addClick));
	}
	
################################### ЯНДЕКС КАРТЫ ###################		
	    public function yamapsAction(Request $request)
    {
		$addClick = false;
		if (isset($_GET['addclick'])) {
			$addClick = true;
			
			if (isset($_POST['title'])) {
				$lat = $_POST['lat'];
				$long = $_POST['long'];
				$height = 0;
				$title = $_POST['title'];
				$newp = new Points();
				$newp->setTitle($title);
				$pp = new Point();
				$result = $pp->setFromLLH($lat, $long, $height);
				$repository = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points');
				$newp->setPoint($pp);
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($newp);
				$em->flush();
				echo "Добавление точки <b>$title</b> успешно выполнено";
				exit;
			}
			
		}
	
		$pts = $this->getDoctrine()->getRepository('PraktikaTestBundle:Points')->findAll();
		return $this->render('PraktikaTestBundle:Default:yamaps.html.twig', array ('pts'=>$pts, 'addClick'=>$addClick));
	}
	
	public function maps2Action (Request $request) {
		if ($request->getMethod()=='POST') {
			$repo = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints');
			// получили точку для отрисовки
			$point = intval($_POST['point']);
			// проверить, что есть
			$dataPoint = $repo->find($point);
			if (!$dataPoint);// error
			else {			
				// search maxDist
				$pointsBased = array();
				$allPoints = $repo->findAll();
				foreach ($allPoints as $v) {
					if ($v->getPoint()->getBase() == $dataPoint->getPoint()->getBase())
						$pointsBased[] = $v;
				}	
				
				// параметры
				$param['width'] = $width = 300; // ширина картинки
				$param['widthLegend'] = $widthLegend = 200; // щирина легенды
				$param['height'] = $height = 300; // высота картинки
				$param['colorCenter'] = $colorCenter = 0; // цвет отрисовки центральной точки
				$param['colorLegend'] = $colorLegend = 0; // цвет отрисовки легенды
				$param['colorPoint'] = $colorPoints = 0; // цвет отрисовки точек
				$param['colorBG'] = $colorBG = 0xFFFFFF; // цвет фона
				$param['sizePoints'] = $sizePoints = 6; // размер рисуемых точек
				$param['font'] = $font = "arial.ttf"; // Шрифт ДОЛЖЕН быть в папке Resources/public/fonts/
				$param['fontSize'] = $fontSize = 10; // размер шрифта	
				$param['metersLabel'] = $metersLabel = "метров"; // свой перевод для meters
				$param['points'] = $pointsBased; // массив точек для отрисовки
				$param['div'] = 'chart'; // div для D3JS
				$param['centerLabel'] = "Центр"; // свой перевод для center
				
				//foreach ($param as $k=>$v) 
					//if (isset($_POST[$k]))
						//$param[$k] = $_POST[$k];
				
	
			$visual = new Visual ($param);
			
			if ($_POST['format']==2) {
				header ("Content-type: image/png");
				echo $visual->getPNG();
				exit;
			}
			if ($_POST['format']==3) {
				$pdf = $visual->getPDF();
				$pdf->Output('doc.pdf', 'I'); 
				exit;
			}
			$script = $visual->getJS();
			return $this->render('PraktikaTestBundle:Default:maps2d3js.html.twig', array('script'=>$script, 'width'=>$width+$widthLegend, 'height'=>$height));
				
			}
		}
		
		$pts = $this->getDoctrine()->getRepository('PraktikaTestBundle:APoints')->findAll();
		$points = array();
		for ($i=0;$i<count($pts);$i++) {
			$base = $pts[$i]->getPoint()->getBase()->__toString();
			$flag = false;
			for ($j=0;$j<$i;$j++)
				if ($pts[$j]->getPoint()->getBase()->__toString()==$base) {
					$flag = true;
					break;
				}
			if (!$flag)
				$points[]=$pts[$i];
		}
		return $this->render('PraktikaTestBundle:Default:maps2.html.twig', array ('pts'=>$points));		
	}
}
