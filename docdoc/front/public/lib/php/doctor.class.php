<?php
use dfs\docdoc\models\DoctorClinicModel;

class doctor
{
    public $id;
    public $sex;
    public $image;
    public $experience_year;

    public $attributes = array(
        'id'               => 0,
        'name'             => '',
        'status'           => '',
        'rewriteName'      => null,
        'clinicId'         => 0,
        'departure'        => 0,

        'rating'           => array(
            'manual'            => 0,
            'education'         => 0,
            'extEducation'      => 0,
            'experience'        => 0,
            'academicDegree'    => 0,
            'clinic'            => 0,
            'opinion'           => 0,
            'total'             => 0
        ),

        'price'            => 0,
        'specialPrice'     => null,
        'experienceYear'   => null,
        'image'            => null,
        'phone'            => null,
        'phoneAppointment' => null,
        'categoryId'       => 0,
        'degreeId'         => 0,
        'rankId'           => 0,
        'sex'              => 0,

        'description'      => array(
            'text'              => '',
            'degree'            => '',
            'education'         => '',
            'association'       => '',
            'spec'              => '',
            'course'            => '',
            'experience'        => ''
        ),

    );

    public function __construct ($id = 0) {
        if($id > 0) {
            $this->getDoctor($id);
        }
    }

    static function getDoctorListXML ($params=array(), $order=array()) {
        $xml = "";
        $addCond = " 1=1 ";
        $addJoin = "";
        $addOrder = "";
        $startPage = 1;
        $step = 10;

        if(count($params) > 0){
            if (isset($params['name']) && !empty($params['name'])) {
                $addCond .= " AND LOWER(t1.name) LIKE  '%".strtolower($params['name'])."%' ";
            }
            if (isset($params['sector']) && intval($params['sector']) > 0) {
                $addCond .= " AND t4.sector_id = ".$params['sector']." ";
                $addJoin .= " LEFT JOIN doctor_sector t4 ON t4.doctor_id = t1.id ";
            }
            if (isset($params['stations']) && !empty($params['stations'])) {
                $addCond .= " t5.undegraund_station_id IN (".implode(',',$params['stations']).") ";
                $addJoin .= " LEFT JOIN underground_station_4_clinic t5 ON t5.clinic_id = t2.id ";
            } elseif(isset($params['district']) && !empty($params['district'])) {
                $addCond .= " t6.id_district=".$params['district']." ";
                $addJoin .= " LEFT JOIN underground_station_4_clinic t5 ON t5.clinic_id = t2.id ";
                $addJoin .= " LEFT JOIN district_has_underground_station t6 ON t6.id_station = t5.undegraund_station_id ";
            } elseif(isset($params['area']) && !empty($params['area']) && !isset($params['district'])) {
                $addCond .= " t6.area_id=".$params['area']." ";
                $addJoin .= " LEFT JOIN underground_station_4_clinic t5 ON t5.clinic_id = t2.id ";
                $addJoin .= " LEFT JOIN area_underground_station t6 ON t6.station_id = t5.undegraund_station_id ";
            }
            if(isset($params['departure'])) {

            }
        }

        if (!empty($order)) {
            switch ($order['name']) {
                case 'price':
                    $addOrder = ' ORDER BY sortPrice '.($order['direction'] === 'asc' ? 'ASC' : 'DESC');
                    break;

                case 'experience':
                    $addOrder = ' ORDER BY experience '.($order['direction'] === 'asc' ? 'DESC' : 'ASC');
                    break;

                case 'rating':
                    $addOrder = ' ORDER BY sortRating '.($order['direction'] === 'asc' ? 'ASC' : 'DESC');
                    break;

                default:
                    $addOrder = ' ORDER BY sortRating DESC, experience_year, id';
            }
        }

        $sql = "SELECT t1.*,
                    CASE WHEN t1.rating = 0 THEN t1.total_rating ELSE t1.rating END AS sortRating,
                    CASE WHEN t1.special_price IS NULL THEN t1.price ELSE t1.special_price END AS sortPrice,
                    CASE WHEN t1.experience_year=0 THEN DATE_FORMAT(NOW(),'%Y') ELSE t1.experience_year END AS experience
                FROM doctor t1
                LEFT JOIN doctor_4_clinic t2 ON t1.id = t2.doctor_id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                LEFT JOIN clinic t3 ON t3.id = t2.clinic_id".$addJoin."
                WHERE t1.status=3
                    AND t3.city_id=". Yii::app()->city->getCityId() ."
                    AND ".$addCond."
                ".$addOrder;

        if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
        if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];

//        list($sql, $str) = pager( $sql, $startPage, $step, "loglist"); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
//        $xml .= $str;
        //echo $str."<br/>";

        $result = query($sql);
        if (num_rows($result) > 0) {
            $xml .= "<DoctorList>";
            while ($row = fetch_object($result)) {

                $doctor = new self($row);
                $doctor->sex = $row->sex;
                $doctor->image = $row->image;
                $doctor->experience_year = $row->experience_year;

                $xml .= "<Element id=\"".$row->id."\">";
                $xml .= "<Name>".$row->name."</Name>";
                $xml .= "<RewriteName>".$row->rewrite_name."</RewriteName>";
                $xml .= "<Price>".$row->price."</Price>";
                $xml .= "<Departure>".$row->departure."</Departure>";
                $xml .= "<Description>".$row->text."</Description>";
                $xml .= "<Degree>".$doctor->getDegree()."</Degree>";
                $xml .= "<Experience>".$doctor->getExperience()."</Experience>";
                if(empty($row->rating))
                    $xml .= "<Rating>".round($row->total_rating,1)."</Rating>";
                else
                    $xml .= "<Rating>".$row->rating."</Rating>";
                $xml .= $doctor->getStationListXml();
                $xml .= $doctor->getSectorListXml();
                $xml .= "</Element>";
            }
            $xml .= "</DoctorList>";
        }
        return $xml;
    }

    public function getOpinionCount () {
        $sql = "SELECT COUNT(*) AS cnt
                FROM doctor_opinion
                WHERE allowed=1
                    AND doctor_id=".$this->id;
        $result = query($sql);
        $item = fetch_object($result);

        return $item->cnt;
    }

    public function getExperience () {
        return (intval(date('Y')) - $this->experience_year);
    }

    public function getAvatarUrl($small = true) {
        if(empty($this->attributes['image'])){
            if($this->attributes['sex'] == 2)
                $name = 'avatarw_default.gif';
            else
                $name = 'avatarm_default.gif';
            return '/img/doctorsNew/'.$name;
        } else {
/*            if (!$this->isUploaded('image')) return null;

            list($smallName, $medName) = $this->createImageNames();
            $url = $this->getUploadUrl('image');
            $path = $this->getUploadPath('image');

            $name = $small ? $smallName : $medName;
*/
            $url = '/img/doctorsNew/';
            if(!$small)
                $name = $this->attributes['id']."_med.jpg";
            else
                $name = $this->attributes['id']."_small.jpg";

            return $url.$name;
        }
    }

    public function getStationListXML () {
        $xml = "";

        $sql = "SELECT t1.id, t1.name, t1.rewrite_name, t1.underground_line_id
                FROM underground_station t1
                INNER JOIN underground_station_4_clinic t2 ON t2.undegraund_station_id=t1.id
                INNER JOIN doctor_4_clinic t3 ON t3.clinic_id=t2.clinic_id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                WHERE t3.doctor_id=".$this->id."
                GROUP BY t1.rewrite_name";
        $result = query($sql);

        if (num_rows($result) > 0) {
            $xml .= "<StationList>";
            while ($row = fetch_object($result)) {
                $xml .= "<Element id=\"".$row->id."\">";
                $xml .= "<Name>".$row->name."</Name>";
                $xml .= "<RewriteName>".$row->rewrite_name."</RewriteName>";
                $xml .= "<LineId>".$row->underground_line_id."</LineId>";
                $xml .= "</Element>";
            }
            $xml .= "</StationList>";
        }

        return $xml;
    }

    public function getSectorListXML () {
        $xml = "";

        $sql = "SELECT t1.id, t1.name, t1.rewrite_name
                FROM sector t1
                INNER JOIN doctor_sector t2 ON t2.sector_id=t1.id
                WHERE t2.doctor_id=".$this->id;
        $result = query($sql);

        if (num_rows($result) > 0) {
            $xml .= "<SectorList>";
            while ($row = fetch_object($result)) {
                $xml .= "<Element id=\"".$row->id."\">";
                $xml .= "<Name>".$row->name."</Name>";
                $xml .= "<RewriteName>".$row->rewrite_name."</RewriteName>";
                $xml .= "</Element>";
            }
            $xml .= "</SectorList>";
        }

        return $xml;
    }

    public function getDegree ()
    {
        $sql = "SELECT t1.text_degree, t2.title AS category, t3.title AS degree, t4.title AS rank
                FROM doctor t1
                LEFT JOIN category_dict t2 ON t2.category_id=t1.category_id
                LEFT JOIN degree_dict t3 ON t3.degree_id=t1.degree_id
                LEFT JOIN rank_dict t4 ON t4.rank_id=t1.rank_id
                WHERE t1.id=".$this->id."
                ";
//        echo $sql;die;
        $result = query($sql);
        $item = fetch_object($result);

        $result = '';
        if($item->category)
            $result .= $item->category.'. ';
        if($item->degree)
            $result .= $item->degree.'. ';
        if($item->rank)
            $result .= $item->rank.'. ';

        if($result == '')
            $result = $item->text_degree;

        return $result;
    }

    public function getRating (){
        if(empty($this->attributes['rating']['manual']) || $this->attributes['rating']['manual'] == 0){
            return str_replace(',', '.', round($this->attributes['rating']['total'],1));
        } else
            return $this->attributes['rating']['manual'];
    }

    public function getExperienceInRus () {
        $str = '';

        if (!empty($this->attributes['experienceYear']) && $this->attributes['experienceYear'] < date('Y')) {
            $experience = date('Y') - $this->attributes['experienceYear'];
            $str .= $experience.' '.RussianTextUtils::caseForNumber($experience, array('год', 'года', 'лет'));
        }

        return $str;
    }

    public function getDoctorXML () {
        $xml = '';

        $attr = $this->attributes;

        $xml .= '<Doctor id="'.$attr['id'].'">';
        $xml .= '<Name>'.$attr['name'].'</Name>';
        $xml .= '<RewriteName>'.$attr['rewriteName'].'</RewriteName>';
        $xml .= '<Departure>'.$attr['departure'].'</Departure>';
        $xml .= '<Rating>'.$this->getRating().'</Rating>';
        $xml .= '<InternalRating>'.$attr['rating']['internal'].'</InternalRating>';
        $xml .= '<Experience>'.$this->getExperienceInRus().'</Experience>';
        $xml .= '<Price>'.$attr['price'].'</Price>';
        $xml .= '<SpecialPrice>'.$attr['specialPrice'].'</SpecialPrice>';
        $xml .= '<OpinionCount>'.$this->getOpinionCount().'</OpinionCount>';
        $xml .= '<ImageURL>/img/doctorsNew/'.$attr['image'].'</ImageURL>';
        $xml .= '<Description>'.$attr['description']['text'].'</Description>';
        $xml .= '<Degree>'.(string)$this->getDegree().'</Degree>';
        $xml .= '<ClinicList>'.arrayToXML($this->getClinicList()).'</ClinicList>';
        $xml .= $this->getSectorListXML();
        $xml .= $this->getStationListXML();
        $xml .= '</Doctor>';

        return $xml;
    }

    public function getDoctor ($id) {
        $id = intval($id);

        if($id > 0) {
            $sql = "SELECT
                        t1.id, t1.name, t1.status, t1.rewrite_name, t1.clinic_id, t1.departure, t1.kids_reception,
                        t1.rating, t1.rating_education, t1.rating_ext_education, t1.rating_experience,
                        t1.rating_academic_degree, t1.rating_opinion, t1.rating_clinic, t1.total_rating,
                        t1.price, t1.special_price, t1.experience_year, t1.image, t1.phone, t1.phone_appointment,
                        t1.category_id, t1.degree_id, t1.rank_id, t1.sex, t1.text, t1.text_degree, t1.experience_year,
                        t1.rating_internal,
                        CASE 
                            WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=1 THEN 'avatar_m_small.gif'
                            WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=2 THEN 'avatar_w_small.gif'
                            ELSE CONCAT(t1.id,'_small.jpg')
                        END AS SmallImg
                    FROM doctor t1
                    WHERE id=$id";
//            echo $sql;
            $result = query($sql);

            if (num_rows($result) == 1) {
                $row = fetch_object($result);

                $this->id = $row->id;
                $this->attributes = array(
                    'id'               => $row->id,
                    'name'             => $row->name,
                    'status'           => $row->status,
                    'rewriteName'      => !empty($row->rewrite_name) ? $row->rewrite_name : $row->id,
                    'clinicId'         => $row->clinic_id,
                    'departure'        => $row->departure,
					'kidsReception'    => $row->kids_reception,

                    'rating'           => array(
                        'manual'            => $row->rating,
                        'education'         => $row->rating_education,
                        'extEducation'      => $row->rating_ext_education,
                        'experience'        => $row->rating_experience,
                        'academicDegree'    => $row->rating_academic_degree,
                        'clinic'            => $row->rating_clinic,
                        'opinion'           => $row->rating_opinion,
                        'total'             => $row->total_rating,
                        'internal'          => $row->rating_internal,
                    ),

                    'price'            => $row->price,
                    'specialPrice'     => (int)$row->special_price,
                    'experienceYear'   => $row->experience_year,
                    'image'            => $row->SmallImg,
                    'phone'            => $row->phone,
                    'phoneAppointment' => $row->phone_appointment,
                    'categoryId'       => $row->category_id,
                    'degreeId'         => $row->degree_id,
                    'rankId'           => $row->rank_id,
                    'sex'              => $row->sex,

                    'description'      => array(
                        'text'              => $row->text,
                        'degree'            => $row->text_degree,
                    )
                );

            }
        }
    }
    
    public function getClinicList() {
        
        $sql = "SELECT id AS Id, name AS Name, rewrite_name AS Alias
                FROM clinic t1
                INNER JOIN doctor_4_clinic t2 ON t2.clinic_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                WHERE t2.doctor_id=".$this->id;
        $result = query($sql);
        
        $data = array();
        if(num_rows($result) > 0){
            while($clinic = fetch_array($result))
                array_push($data, $clinic);
            
        }
        
        return $data;
    }

}
?>
