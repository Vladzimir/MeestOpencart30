<?php
class ModelExtensionShippingMeest2 extends Model {

    private $extension = 'meest2'; //'meest2.meest2'

    private $poshtomatType = '23c4f6c1-b1bb-49f7-ad96-9b014206fe8e';

    private $warehouseType = array(
        '91cb8fae-6a94-4b1d-b048-dc89499e2fe5',
        '0c1b0075-cd44-49d1-bd3e-094da9645919',
        'acabaf4b-df2e-11eb-80d5-000c29800ae7',
        'ac82815e-10fe-4eb7-809a-c34be4553213',
    );

    public function getResults($typeId, $cityName, $search) {
        $sql = "SELECT br_id, num, street_ua as street,
            street_number, type_public_ua as type_public, `lng`, `lat` FROM "
            . DB_PREFIX . "meest2_branch WHERE `city_ua`='$cityName'";

        $parasits = array(
            'Відділення', 'Отделение',
            '№', 'Віділення',
            'віділення', 'відділення',
            'отделение',
            'вул', 'ул','st',
            'пров','пер',
            'шоссе','шосе',
            'просп','ave',
            'blvd','бул'
        );
        $searches = preg_split("/[\s,.]+/", $search);
        $searches = array_diff($searches, $parasits);
        $searches = array_filter($searches);
        $searches = implode('', $searches);

        if (is_numeric($searches)) {
            $sql .= " AND `num` LIKE '$searches%'";
        } else {
            $sql .= " AND (CONCAT(`street_ua`, `street_number`) LIKE '%$searches%'";
            $sql .= " OR CONCAT(`street_ru`, `street_number`) LIKE '%$searches%'";
            $sql .= " OR CONCAT(`street_en`, `street_number`) LIKE '%$searches%')";
        }

        if (is_array($typeId)) {
			$sql .= " AND type_id IN ('" . implode("','", $typeId) . "')";
		} else {
			$sql .= " AND type_id = '" . $this->db->escape($typeId) . "'";
		}

        $sql .= " ORDER BY num LIMIT 20";

        return $this->db->query($sql)->rows;
	}

    public function getCity($typeId, $search)
    {
        $sql = "SELECT DISTINCT `region_ua`, `district_ua`, `city_ua`, `city_id` FROM " . DB_PREFIX .
            "meest2_branch WHERE (`city_ua` LIKE '" . $this->db->escape($search) . "%' OR `city_ru` LIKE '"
            . $this->db->escape($search) . "%' OR `city_en` LIKE '" . $this->db->escape($search) . "%')";

        if (is_array($typeId)) {
            $sql .= " AND type_id IN ('" . implode("','", $typeId) . "')";
        } else {
            $sql .= " AND type_id = '" . $this->db->escape($typeId) . "'";
        }

        $sql .= " ORDER BY `city_ua` LIMIT 20";

        return $this->db->query($sql)->rows;
    }

    public function getCities($region_id, $search = '')
    {
        $sql = "SELECT
            c.`city_id` AS id,
            c.`type_ua` AS type,
            c.`name_ua` AS name,
            r.`region_name_ua` AS region
        FROM `" . DB_PREFIX . "meest2_cities` c
        LEFT JOIN `" . DB_PREFIX . "meest2_regions` r ON c.`region_id` = r.`region_id`
        WHERE 1";

        if ($region_id) {
            $region_query = $this->db->query("SELECT `region_id`
                                      FROM `" . DB_PREFIX . "meest2_regions`
                                      WHERE `zone_id` = '" . (int)$region_id . "'");
            if ($region_query->num_rows) {
                $region_id = $region_query->row['region_id'];
                $sql .= " AND c.`region_id` = '" . $this->db->escape($region_id) . "'";
            }
        }

        $search = urldecode($search);
        if ($search) {
            $sql .= " AND (c.`name_ua` LIKE '" . $this->db->escape($search) . "%' OR c.`name_ru` LIKE '" . $this->db->escape($search) . "%')";
        }

        $sql .= " ORDER BY
            CASE
                WHEN c.`name_ua` LIKE '" . $this->db->escape($search) . "' THEN 1
                WHEN c.`name_ua` LIKE '" . $this->db->escape($search) . "%' THEN 2
                ELSE 3
            END,
            CHAR_LENGTH(c.`name_ua`),
            c.`name_ua`";

        $sql .= " LIMIT 50";

        return $this->db->query($sql)->rows;
    }

    public function getBranches($city_id, $search = '', $types) {
        $city_id = urldecode($city_id);
        $search = urldecode($search);

        $sql = "SELECT `branch_id` AS id,
                   `short_name` AS description,
                   `address_more_information`
            FROM `" . DB_PREFIX . "meest2_branch`
            WHERE `city_id` = '" . $this->db->escape($city_id) . "'";

        if (!empty($types)) {
            if (is_array($types)) {
                $escaped_types = array_map([$this->db, 'escape'], $types);
                $sql .= " AND `branch_type_id` IN ('" . implode("','", $escaped_types) . "')";
            } else {
                $sql .= " AND `branch_type_id` = '" . $this->db->escape($types) . "'";
            }
        }

        if ($search) {
            $sql .= " AND (`short_name` LIKE '%" . $this->db->escape($search) . "%'
                 OR `address` LIKE '%" . $this->db->escape($search) . "%')";
        }

        $sql .= " ORDER BY `short_name` LIMIT 50";

        return $this->db->query($sql)->rows;
    }

    public function getStreets($city_id, $search = '') {
        $city_id = urldecode($city_id);
        $search = urldecode($search);

        $sql = "SELECT `street_id` AS id, CONCAT(`type_ua`, ' ', `name_ua`) AS description, CONCAT(`type_ua`, ' ', `name_ua`) AS full_description
            FROM `" . DB_PREFIX . "meest2_streets`
            WHERE `city_id` = '" . $this->db->escape($city_id) . "'";

        if ($search) {
            $sql .= " AND (`name_ua` LIKE '" . $this->db->escape($search) . "%' OR `name_ru` LIKE '" . $this->db->escape($search) . "%')";
        }

        $sql .= " ORDER BY `name_ua` LIMIT 50";

        return $this->db->query($sql)->rows;
    }

    public function getQuote($address) {
        $data = $this->load->language('extension/shipping/meest2');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone 
                                    WHERE geo_zone_id = '" . (int)$this->config->get('free_geo_zone_id') . "' 
                                    AND country_id = '" . (int)$address['country_id'] . "' 
                                    AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('shipping_meest2_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $image = '/image/catalog/meest2/meest_logo.svg';
        $image_html = "<img style='width:70px' src='$image' alt='{$this->language->get('text_title')}' title='{$this->language->get('text_title')}' />";
        $image_html_service = "<img style='width:20px' src='$image' alt='' title='' />";

        if ($this->cart->getSubTotal() < $this->config->get('free_total')) {
            $status = false;
        }

        if (!$status) {
            return [];
        }

        $method_data = [];
        $quote_data = [];

        $meestSessionData = isset($this->session->data['meest_data']) ? $this->session->data['meest_data'] : [];
        $cityUUID       = isset($meestSessionData['city_UUID']) ? $meestSessionData['city_UUID'] : '';
        $address1UUID   = isset($meestSessionData['address_1_UUID']) ? $meestSessionData['address_1_UUID'] : '';

        $shippingMethod = isset($meestSessionData['shipping_method']) ? $meestSessionData['shipping_method'] : '';

        $shipping_meest_service = $this->config->get('shipping_meest2_service');

        $checkoutCode = (isset($this->request->get['route']) ? $this->request->get['route'] : '') === 'checkout/shipping_method';

        if ($shipping_meest_service) {
            $length = count($shipping_meest_service);
            $costs = $this->config->get('shipping_meest2_cost');
            $errorMessage = '';
            $receiverMethod = str_replace('meest2.', '', $shippingMethod);
            $enabledCalculationInCheckout = $this->config->get('shipping_meest2_calculation_in_checkout');

            if ($cityUUID && $address1UUID && $enabledCalculationInCheckout) {
                $cart_data = $this->prepareApiData($cityUUID, $address1UUID, $shippingMethod);

                $api_url = 'https://api.meest.com/v3.0/openAPI/calculate';
                $api_response = $this->callMeestAPIv3($api_url, $cart_data);

                if ($api_response) {
                    if (isset($api_response['status']) && $api_response['status'] === 'error') {
                        $message        = isset($api_response['info']['message']) ? $api_response['info']['message'] : '';
                        $messageDetails = isset($api_response['info']['messageDetails']) ? $api_response['info']['messageDetails'] : '';
                        $messageForShowMeestMethods = $this->language->get('text_error_shipping_checkout');
                        $errorMessage = trim($message . ' ' . $messageDetails . "<br>" . $messageForShowMeestMethods);

                        if ($errorMessage === '') {
                            $errorMessage = 'Error calculating shipping.';
                        }

                        unset($this->session->data['meest_data']['address_1_UUID']);

                    } elseif (isset($api_response['result']['costServices'])) {
                        $costs[$receiverMethod] = $api_response['result']['costServices'];
                        $costs['def'] = $api_response['result']['costServices'];
                    } else {
                        $errorMessage = 'Incorrect response from the delivery service.';
                        foreach ($shipping_meest_service as $service) {
                            $costs[$service] = 0;
                        }
                    }
                } else {
                    $errorMessage = 'No response from shipping API.';
                    foreach ($shipping_meest_service as $service) {
                        $costs[$service] = 0;
                    }
                }
            }

            foreach ($shipping_meest_service as $key => $service) {
                if ($checkoutCode) {
                    $errorData = false;
                } else {
                    $errorData = ($receiverMethod === $service && !empty($errorMessage)) ? $errorMessage : false;
                }
                $quote_data[$service] = [
                    'code'         => 'meest2.' . $service,
                    'title'        => $image_html_service . " Meest: " . $this->language->get('text_title_' . $service),
                    'cost'         => !empty($costs[$service]) ? $costs[$service] : 0,
                    'tax_class_id' => $this->config->get('shipping_meest2_tax_class_id'),
                    'text'         => empty($costs[$service])
                        ? ''
                        : $this->currency->format(
                            $this->tax->calculate(
                                (isset($costs[$service]) ? $costs[$service] : 0),
                                $this->config->get('meest2_tax_class_id'),
                                $this->config->get('config_tax')),
                            $this->session->data['currency']
                        ),
                    'error' => $errorData
                ];

                if ($key == $length - 1) {
                    $html = $this->load->controller('extension/module/meest2', $data);
                    $quote_data[$service]['text'] .= '</label>' . $html;
                }
            }

            if($checkoutCode && !empty($errorMessage)) {
                $method_data = [
                    'code' => $this->extension,
                    'title' => $image_html . $this->language->get('text_title'),
                    'quote' => $quote_data,
                    'sort_order' => $this->config->get('shipping_meest2_sort_order'),
                    'error' => $errorMessage
                ];
            } else {
                $method_data = [
                    'code' => $this->extension,
                    'title' => $image_html . $this->language->get('text_title'),
                    'quote' => $quote_data,
                    'sort_order' => $this->config->get('shipping_meest2_sort_order'),
                    'error' => false
                ];
            }
        }

        return $method_data;
    }

    public function callMeestAPIv3($url, $data) {
        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "Content-Type: application/json",
                'token: ' . $this->auth()
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    public function prepareApiData($recipientCityUUID, $recipientAddress1UUID, $shippingMethod) {
        $products = $this->cart->getProducts();

        $totalQuantity = 0;
        $totalWeight   = 0;
        $maxLength     = 0;
        $maxWidth      = 0;
        $maxHeight     = 0;

        foreach ($products as $product) {
            $totalQuantity += $product['quantity'];
            $totalWeight   += $product['weight'];

            if ($product['length'] > $maxLength) {
                $maxLength = $product['length'];
            }
            if ($product['width'] > $maxWidth) {
                $maxWidth = $product['width'];
            }
            if ($product['height'] > $maxHeight) {
                $maxHeight = $product['height'];
            }
        }

        $placesItems = [
            'quantity'  => $totalQuantity,
            'weight'    => $totalWeight,
            'insurance' => 0,
            'length'    => $maxLength,
            'width'     => $maxWidth,
            'height'    => $maxHeight,
        ];

        $receiverMethod = str_replace('meest2.', '', $shippingMethod);
        $capitalizedMethod = ucfirst($receiverMethod);
        if ($capitalizedMethod === 'Postomat') {
            $capitalizedMethod = 'Branch';
        } elseif ($capitalizedMethod === 'Courier') {
            $capitalizedMethod = 'Door';
            $recipientAddress1UUID = '';
        } else {
            $capitalizedMethod = 'Branch';
        }

        $data = [
            'sendingDate' => '',
            'contractID'  => '',
            'sender'      => [
                'branchID' => $this->config->get('shipping_meest2_sender_branch'),
                'cityID'   => $this->config->get('shipping_meest2_sender_city'),
                'service'  => "Branch"
            ],
            'receiver'    => [
                'branchID'  => $recipientAddress1UUID,
                'cityID'    => $recipientCityUUID,
                'service'   => $capitalizedMethod
            ],
            'placesItems' => [$placesItems]
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function auth() {
        $url = 'https://api.meest.com/v3.0/openAPI/auth';

        $authMode = $this->config->get('shipping_meest2_auth_mode');
        if($authMode === "api_key"){
            return $this->config->get('shipping_meest2_api_key');
        } elseif ($authMode === "default"){
            if($this->config->get('shipping_meest2_login') && $this->config->get('shipping_meest2_password')){
                $data = json_encode([
                    'username' => $this->config->get('shipping_meest2_login'),
                    'password' => $this->config->get('shipping_meest2_password')
                ]);
            } else {
                $this->session->data['error_warning_meest'] = 'Problems with getting a token, enter your login and password';
                $this->response->redirect($this->url->link('extension/shipping/meest2', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
            }
        } else {
            $responseData['status']  = 'error';

            return $responseData;
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($responseData['status'] === 'OK') {
            return $responseData['result']['token'];
        } else {
            return $responseData;
        }
    }

    public function getShippingMethod() {
        if (!empty($this->request->post['shipping_method']) && is_string($this->request->post['shipping_method'])) {
            $data = explode('.', $this->request->post['shipping_method']);
        } elseif (!empty($this->request->post['shipping']) && is_string($this->request->post['shipping'])) {
            $data = explode('.', $this->request->post['shipping']);
        } elseif (isset($this->session->data['shipping_method'], $this->session->data['shipping_method']['code']) && is_string($this->session->data['shipping_method']['code'])) {
            $data = explode('.', $this->session->data['shipping_method']['code']);
        } else {
            $data = array('', '');
        }

        return array (
            'method'     => $data[0],
            'sub_method' => $data[1]
        );
    }

    public function getMeest2Cities($region = '', $search = '') {

        $regionId = $this->getRegionIdByZone($region);

        if (!$regionId) {
//            $sql = "SELECT `name_ua`, `type_ua` FROM `" . DB_PREFIX . "meest2_cities` WHERE 1";
            $sql = "
                SELECT `name_ua`, `type_ua`
                FROM `" . DB_PREFIX . "meest2_cities`
                WHERE `type_ua` = 'місто'
            ";
        } else {
            $escaped_region = $this->db->escape($regionId);
            $sql = "
                SELECT `name_ua`, `type_ua`
                FROM `" . DB_PREFIX . "meest2_cities`
                WHERE `region_id` = '" . $escaped_region . "'
            ";
        }

        $sql .= " ORDER BY `name_ua`";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getBranchesByCityMeest($city_name, $types) {

        if ($types == 'postomat') {
            $types = $this->poshtomatType;
        } else {
            $types = $this->warehouseType;
        }

        $sql = "
            SELECT `short_name`, `branch_type`
            FROM `" . DB_PREFIX . "meest2_branch`
            WHERE `city_ua` = '" . $this->db->escape($city_name) . "'
        ";

        if (!empty($types)) {
            if (is_array($types)) {
                $escaped_types = array_map([$this->db, 'escape'], $types);
                $sql .= " AND `branch_type_id` IN ('" . implode("','", $escaped_types) . "')";
            } else {

                $sql .= " AND `branch_type_id` = '" . $this->db->escape($types) . "'";
            }
        }

        $sql .= " ORDER BY `branch_number`";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getRegionIdByZone($zone_id) {
        $zone_id_escaped = $this->db->escape($zone_id);

        $query = $this->db->query("
            SELECT `region_id`
            FROM `" . DB_PREFIX . "meest2_regions`
            WHERE `zone_id` = '" . $zone_id_escaped . "'
            LIMIT 1
        ");

        if (isset($query->row['region_id'])) {
            return $query->row['region_id'];
        } else {
            return null;
        }
    }
}
