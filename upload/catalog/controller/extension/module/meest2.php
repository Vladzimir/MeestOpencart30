<?php
class ControllerExtensionModuleMeest2 extends Controller {
    private $poshtomatType = '23c4f6c1-b1bb-49f7-ad96-9b014206fe8e';
    private $warehouseType = array(
        '91cb8fae-6a94-4b1d-b048-dc89499e2fe5',
        '0c1b0075-cd44-49d1-bd3e-094da9645919',
        'acabaf4b-df2e-11eb-80d5-000c29800ae7',
        'ac82815e-10fe-4eb7-809a-c34be4553213',
    );

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language('extension/shipping/meest2');
    }

//    public function search(){
//		$json = [];
//		$this->load->model('extension/shipping/meest2');
//
//		if (isset($this->request->get['service'])) {
//			switch ($this->request->get['service']) {
//				case 'postomat':
//                    $json = $this->getBranches(
//                        $this->poshtomatType,
//                        $this->request->get['name']
//                    );
//
//					break;
//				case 'warehouse':
//                    $json = $this->getBranches(
//                        $this->warehouseType,
//                        $this->request->get['name']
//                    );
//
//					break;
//				default:
//					$json[] = [
//						'br_id'      => 0,
//						'name'       => $this->language->get('text_meest2_branch_not_found'),
//						'type'       => 0,
//						'address'    => '',
//						'anyfield'   => 0
//					];
//			}
//		}
//
//        if (empty($json)) {
//            $json[] = [
//                'br_id'      => 0,
//                'name'       => $this->language->get('text_meest2_branch_not_found'),
//                'type'       => 0,
//                'address'    => '',
//                'anyfield'   => 0
//            ];
//        }
//
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
//	}

//    public function searchCity(){
//        $this->load->model('extension/shipping/meest2');
//        $cityName = trim($this->request->get['filter_name']);
//
//        switch ($this->request->get['service']) {
//            case 'postomat':
//                $typeId = $this->poshtomatType;
//                break;
//            case 'warehouse':
//                $typeId = $this->warehouseType;
//                break;
//        }
//
//        $cities = $this->getCity($typeId, $cityName);
//
//        if (empty($cities)) {
//            $cities = [[
//                'place' => $this->language->get('text_meest2_city_not_found'),
//                'city' => '',
//                'city_id' => '',
//            ]];
//        }
//
//        $this->response->addHeader('Content-Type: application/json');
//        $this->response->setOutput(json_encode($cities));
//    }

//    public function searchCityWithDelivery()
//    {
//        $meest = new Meest($this->registry);
//        $parameters = ['search_beginning' => trim($this->request->get['filter_name'])];
//        $result = $meest->geo_localities($parameters);
//        $cityWithDelivery = [];
//
//        if (!empty($result['result'])) {
//            $cityFilter = array_filter($result['result'], function ($item) {
//                return $item['data']['is_delivery_in_city'] === false;
//            });
//
//            $cityWithDelivery = array_map(function ($item) {
//                $data = $item['data'];
//                $format = $this->language->get('responce_search_city_format_with_district');
//                $regionToLower = mb_convert_case($data['reg'], MB_CASE_LOWER);
//                $regionToUpperTitle = mb_convert_case($regionToLower, MB_CASE_TITLE);
//
//                if ($data['dis'] === $data['n_ua']) {
//                    $format = $this->language->get('responce_search_city_format_without_district');
//                }
//
//                return [
//                    'city_id' => $data['city_id'],
//                    'message' => null,
//                    'address' => sprintf(
//                        $format,
//                        $regionToUpperTitle,
//                        $data['dis'],
//                        $data['n_ua']
//                    ),
//                ];
//            }, $cityFilter);
//        }
//
//        if (empty($cityWithDelivery)) {
//            $cityWithDelivery[] = [
//                'city_id' => null,
//                'message' => 'В цьому населеному пункті немає адресної доставки',
//                'address' => '',
//            ];
//        }
//
//        $this->response->addHeader('Content-Type: application/json');
//        $this->response->setOutput(json_encode(array_slice($cityWithDelivery, 0, 20)));
//    }

//    public function searchStreets()
//    {
//        $meest = new Meest($this->registry);
//        $parameters = [
//            'city_id' => $this->request->get['city_id'],
//            'search_beginning' => trim($this->request->get['filter_name'])
//        ];
//
//        $result = $meest->geo_streets($parameters);
//        $streets = [];
//
//        if (!empty($result['result'])) {
//            $streets = array_map(function ($item) {
//                $format = '%s %s';
//
//                return [
//                    'message' => null,
//                    'street' => sprintf(
//                        $format,
//                        $item['t_ua'],
//                        $item['ua']
//                    ),
//                ];
//            }, $result['result']);
//        }
//
//        $this->response->addHeader('Content-Type: application/json');
//        $this->response->setOutput(json_encode(array_slice($streets, 0, 20)));
//    }

	public function index(){
		$data = $this->load->language('extension/module/meest2');

		return $this->load->view('extension/module/meest2', $data);
	}

    public function save() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $address = isset($this->request->post['address']) ? $this->request->post['address'] : '';
            $house = isset($this->request->post['house']) ? $this->request->post['house'] : '';
            $flat = isset($this->request->post['flat']) ? $this->request->post['flat'] : '';

            $full_address = $address;
            if (!empty($house)) {
                $full_address .= ' ' . $this->language->get('text_meest2_house') . ' ' . $house;
            }
            if (!empty($flat)) {
                $full_address .= ' ' . $this->language->get('text_meest2_flat') . ' ' . $flat;
            }

            $this->session->data['shipping_address']['address_1'] = $full_address;

            if (isset($this->session->data['simple']['shipping_address'])) {
                $this->session->data['simple']['shipping_address']['address_1'] = $full_address;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'status' => 'success',
                'session' => $this->session->data['shipping_address']['address_1']
            ]));
        }
    }


    private function place($branch){
		$place = '';

		if (!empty($branch['lat'])) {
			$lng = $branch['lat'];
			$d = floor( $lng);
			$m = floor(($lng - $d) * 60);
			$s = round(($lng - $d - $m/60) * 3600,2);
			$place = 'place/' . $d . '%C2%B0' . $m . "'" . $s . '%22N+';
			$lat = $branch['lng'];
			$d = floor( $lat);
			$m = floor(($lat - $d) * 60);
			$s = round(($lat - $d - $m/60) * 3600,2);

			$place .= $d . '%C2%B0' . $m . "'" . $s . '%22E';
		}

		return $place;
	}

    private function getBranches($typeId, $name)
    {
        $branches = $this
            ->model_extension_shipping_meest2
            ->getResults(
                $typeId,
                $this->request->get['city_name'],
                trim($this->request->get['filter_name'])
            );
        $name = mb_convert_case($name, MB_CASE_TITLE);
        $result = [];

        foreach ($branches as $branch) {
            $place = $this->place($branch);
            $result[] = [
                'br_id'      => $branch['br_id'],
                'name'       => sprintf($name.' № %s, %s, %s',$branch['num'],$branch['street'],$branch['street_number']),
                'type'       => $branch['type_public'],
                'address'    => sprintf($name.' № %s, %s, %s',$branch['num'],$branch['street'],$branch['street_number']),
                'anyfield'   => $place
            ];
        }

        return $result;
    }

    private function getCity($typeId, $cityName)
    {
        $cities = $this->model_extension_shipping_meest2->getCity($typeId, $cityName);
        $result = [];

        foreach ($cities as $city) {
            $regionToLower = mb_convert_case($city['region_ua'], MB_CASE_LOWER);
            $regionToUpperTitle = mb_convert_case($regionToLower, MB_CASE_TITLE);
            $strFormat = $this->language->get('responce_search_city_format_with_district');

            if ($city['district_ua'] === $city['city_ua']) {
                $strFormat = $this->language->get('responce_search_city_format_without_district');;
            }

            $result[] = [
                'place' => sprintf(
                    $strFormat,
                    $regionToUpperTitle,
                    $city['district_ua'],
                    $city['city_ua']
                ),
                'city' => $city['city_ua'],
                'city_id' => $city['city_id'],
            ];
        }

        return $result;
    }

    public function getMeestData() {
        $json = [];

        $this->load->model('extension/shipping/meest2');
        $model_name = 'model_extension_shipping_meest2';

        if (isset($this->request->post['action'])) {
            $action = $this->request->post['action'];
        } else {
            $action = '';
        }

        if (isset($this->request->post['filter'])) {
            $filter = trim($this->request->post['filter']);
        } else {
            $filter = '';
        }

        if (isset($this->request->post['search'])) {
            $search = trim($this->request->post['search']);
        } else {
            $search = '';
        }

        if ($action === 'getCities') {
            $json = $this->$model_name->getCities($filter, $search);
        } elseif ($action === 'getBranches') {
            $json = $this->$model_name->getBranches($filter, $search, $this->warehouseType);
        } elseif ($action === 'getStreets') {
            $json = $this->$model_name->getStreets($filter, $search);
        } elseif ($action === 'getPoshtomat') {
            $json = $this->$model_name->getBranches($filter, $search, $this->poshtomatType);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function saveUuid() {
        $enabledCalculationInCheckout = $this->config->get('shipping_meest2_calculation_in_checkout');

        if ($enabledCalculationInCheckout) {
            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                $this->session->data['meest_data']['city_UUID'] = isset($this->request->post['cityUuid']) ? $this->request->post['cityUuid'] : '';
                $this->session->data['meest_data']['address_1_UUID'] = isset($this->request->post['addressUuid']) ? $this->request->post['addressUuid'] : '';
                $this->session->data['meest_data']['shipping_method'] = isset($this->request->post['shippingMethod']) ? $this->request->post['shippingMethod'] : '';
            }
        } else {
            unset($this->session->data['meest_data']['city_UUID']);
            unset($this->session->data['meest_data']['address_1_UUID']);
            unset($this->session->data['meest_data']['shipping_method']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'status' => 'success',
        ]));
    }
}
