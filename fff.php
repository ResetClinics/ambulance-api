<?php
header('Content-Type: text/html; charset=utf-8');

$lead_id = $_POST['leads']['status'][0]['id'];

$subdomain = 'af4040148';


$data = getData();

function getData(){
    $mysqli = new mysqli (
        'localhost',
        'af4040ki_crm',
        'jMlgmh3*',
        'af4040ki_crm'
    );

    $db = new MysqliDb ($mysqli);

    $db->where ("id", 1);

     return $db->getOne ("data");
}



$link = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads/'.$lead_id;
$access_token = $data['access_token'];
$headers = [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json',
];
$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$code = (int)$code;
$errors = array(
    301 => 'Moved permanently',
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
);
try
{
    if ($code != 200 && $code != 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
    }
} catch (Exception $E) {
    die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
}
$result = json_decode($out, true);
$custom_fields_values = $result['custom_fields_values'];



$price = $result['price'];

//получение контакта
$link = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads/'.$lead_id.'/links';
$access_token = $data['access_token'];
$headers = [
    'Authorization: Bearer ' . $access_token
];

$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$code = (int)$code;
$errors = [
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
];

try
{
    if ($code < 200 || $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }
}
catch(\Exception $e)
{
    die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}
$Response = json_decode($out, true);

$links_embedded = $Response['_embedded']['links'];
foreach($links_embedded as $one_link) {
    if ($one_link['to_entity_type'] == 'contacts' && $one_link['metadata']['main_contact'] == 1) {
        $this_contact_id = $one_link['to_entity_id'];
    }
}

$link = 'https://' . $subdomain . '.amocrm.ru/api/v4/contacts/'.$this_contact_id;
$access_token = $data['access_token'];
$headers = [
    'Authorization: Bearer ' . $access_token
];

$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$code = (int)$code;
$errors = [
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
];

try
{
    if ($code < 200 || $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }
}
catch(\Exception $e)
{
    die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}
$Response = json_decode($out, true);

$client_name = $Response['name'];
$contact_custom_fields_values = $Response['custom_fields_values'];

foreach ($contact_custom_fields_values as $one_contact_custom_fields_value) {
    if ($one_contact_custom_fields_value['field_id'] == 604157) {
        $client_phone = $one_contact_custom_fields_value['values'][0]['value'];
    }
}

$fio = $Response['name'];

//получение контакта
$send_phone = 0;
foreach ($custom_fields_values as $one_field) {
    if ($one_field['field_id'] == 879807) {
        $nomer_zayavki = $one_field['values'][0]['value'];
    }
    if ($one_field['field_id'] == 880453) {
        $date_time = date("d.m.Y H:i:s", $one_field['values'][0]['value']);
    }
    if ($one_field['field_id'] == 870903) {
        $address = $one_field['values'][0]['value'];
    }

    if ($one_field['field_id'] == 875863) {
        $brigada = $one_field['values'][0]['value'];
    }
    if ($one_field['field_id'] == 880527) {
        $nazologiya = $one_field['values'][0]['value'];
    }

    if ($one_field['field_id'] == 870907) {
        $age = $one_field['values'][0]['value'];
    }
    if ($one_field['field_id'] == 870945) {
        $primechaniye = $one_field['values'][0]['value'];
    }
    if ($one_field['field_id'] == 884333) {
        $hz = $one_field['values'][0]['value'];
    }
    if ($one_field['field_id'] == 960101) {
        $lead_type = $one_field['values'][0]['value'];
    }
    if ($one_field['field_id'] == 896921) {
        if ((int) $one_field['values'][0]['value'] === 1) {
            $send_phone = (int) $one_field['values'][0]['value'];
            //echo 'Если значение равно 1, то !!!$send_phone = '.$send_phone.'<br><br>';
        } else {
            //echo 'Если значение НЕ равно 1, то $send_phone = '.$send_phone.'<br><br>';
        }
    }


}







//Получение врача и админа на сегодня
$employees = getEmployees($brigada, $subdomain, $data);

$administrator = $employees['admin'];
$vrach = $employees['doctor'];

function  getEmployees($brigada, $subdomain, $data,){

    switch ($brigada) {
        case 1:
            $check_status = 38792956;
            break;
        case 2:
            $check_status = 38792959;
            break;
        case 3:
            $check_status = 38792962;
            break;
        case 4:
            $check_status = 38816761;
            break;
        case 5:
            $check_status = 38816764;
            break;
        case 6:
            $check_status = 38816767;
            break;
        case 7:
            $check_status = 42790108;
            break;
        case 8:
            $check_status = 42790111;
            break;
        case 9:
            $check_status = 42790114;
            break;
        case 10:
            $check_status = 42790117;
            break;
        case 11:
            $check_status = 42790120;
            break;
        case 12:
            $check_status = 53996154;
            break;
        case 13:
            $check_status = 61367254;
            break;
        case 14:
            $check_status = 61367258;
            break;
        case 15:
            $check_status = 61417266;
            break;
        case 16:
            $check_status = 61417270;
            break;
    }



    $query_data = array('status' => $check_status);
    $link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?'.http_build_query($query_data);

    $access_token = $data['access_token'];
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];
    $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $code = (int)$code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    );
    try
    {
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
    $Response = json_decode($out, true);
    $Response = $Response['_embedded']['items'];

    foreach ($Response as $one_brigada_people) {
        $tags = $one_brigada_people['tags'];
        foreach ($tags as $one_tag) {
            if ($one_tag['name'] == 'Врач') {
                $vrach = $one_brigada_people['name'];
            }
            if ($one_tag['name'] == 'Администратор') {
                $administrator = $one_brigada_people['name'];
            }
        }
    }
    return [
        'admin' =>$administrator,
        'doctor' => $vrach
    ];
}






$message = createMessage(
    $nomer_zayavki,
    $lead_type,
    $send_phone,
    $client_phone,
    $brigada,
    $price,
    $vrach,
    $administrator,
    $date_time,
    $fio,
    $address,
    $nazologiya,
    $age,
    $hz,
    $primechaniye
);

function createMessage(
    $nomer_zayavki,
    $lead_type,
    $send_phone,
    $client_phone,
    $brigada,
    $price,
    $vrach,
    $administrator,
    $date_time,
    $fio,
    $address,
    $nazologiya,
    $age,
    $hz,
    $primechaniye
){
    $message = '';

    $message .= 'Заявка №: '. $nomer_zayavki.PHP_EOL;
    $message .= 'Тип заявки: '.$lead_type.PHP_EOL;
    if ((int) $send_phone > 0) {
        $message .= 'Телефон: '. $client_phone.PHP_EOL;
    }
    $message .= 'Бригада №: '. $brigada.PHP_EOL;
    $message .= 'Сумма: '. $price.PHP_EOL.PHP_EOL;

    $message .= 'Врач: '. $vrach.PHP_EOL;
    $message .= 'Администратор: '. $administrator.PHP_EOL;
    $message .= 'Время прибытия: '. $date_time.PHP_EOL.PHP_EOL;


    $message .= 'ФИО: '. $fio.PHP_EOL;
    $message .= 'Адрес: '. $address.PHP_EOL.PHP_EOL;

    $message .= 'Нозология: '. $nazologiya.PHP_EOL;
    $message .= 'Возраст: '. $age.PHP_EOL;
    $message .= 'ХЗ: '. $hz.PHP_EOL.PHP_EOL;


    $message .= 'Примечание: '. $primechaniye.PHP_EOL;
    return $message;
}


addEmployees($lead_id,$vrach, $administrator, $subdomain, $data);

function addEmployees($lead_id,$vrach, $administrator, $subdomain, $data){


    $leads_data = array(
        'id' => $lead_id,
        'custom_fields_values' => array(
            array(
                'field_id' => 873881,
                'values' => array(
                    array(
                        'value' => $vrach
                    )
                )
            ),
            array(
                'field_id' => 873879,
                'values' => array(
                    array(
                        'value' => $administrator
                    )
                )
            ),
        )
    );

    $link = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads/'.$lead_id;
    $access_token = $data['access_token'];
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];
    $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($leads_data));
    curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $code = (int)$code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    );
    try
    {
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
}






leadAddComment($message, $lead_id, $subdomain, $data);








// Добавление примечания в сделку об отправленном сообщении
function leadAddComment($message, $lead_id, $subdomain, $data){
    $notes_data = array(
        array(
            'created_by' => 0,
            'note_type' => 'common',
            'params' => array(
                'text' => 'Бригаде отправлено сообщение:'.PHP_EOL.$message
            )
        )
    );

    $link = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads/'.$lead_id.'/notes';
    $access_token = $data['access_token'];
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];
    $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($notes_data));
    curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $code = (int)$code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    );
    try
    {
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
}











exit('ok');

