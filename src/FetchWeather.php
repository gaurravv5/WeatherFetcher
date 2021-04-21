<?php

class FetchWeather
{
	protected $apiKey = 'b5bcbb875dmshba62fd8a0f1e4f4p163fa0jsn0a1f1ffd2506';
	protected $apiUrl = 'https://community-open-weather-map.p.rapidapi.com/weather?q=';
	protected $table = "weather";


	public function fetchAndSave($city, $dbh) {
		try {
			$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => $this->apiUrl . urldecode($city),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"x-rapidapi-host: community-open-weather-map.p.rapidapi.com",
					"x-rapidapi-key: " . $this->apiKey,
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				throw new Exception("cURL Error #:" . $err);
			} else {
				$this->saveWeather(json_decode($response,1), $dbh);
			}
		} catch (\Exception $e) {
			error_log($e->getMessage());
			echo "There is an error while requesting the API.";
		}
	} 

	protected function saveWeather($weatherData, $dbh) {
		try {
			$sql = "INSERT INTO " . $this->table . " (city, temperature, created_at) VALUES (:city, :temp, :timestamp);";
			$sth = $dbh->prepare($sql);
			$sth->execute(array(':city' => $weatherData['name'], ':temp' => $weatherData['main']['temp'], ':timestamp' => date('Y-m-d H:i:s', $weatherData['dt'])));

			return true;
		} catch (\Exception $e) {
			error_log($e->getMessage());
			echo "There is an error while saving the data.";
		}
	}

	public function getLastestWeather($dbh, $city = null) {
		try {
			$sql = '';
			$res = '';
			if (!is_null($city)) {
				$sql = "SELECT temperature FROM " . $this->table . " WHERE `city` = :city ORDER BY created_at DESC LIMIT 1";
				$sth = $dbh->prepare($sql);
				$sth->execute(array(':city' => $city));
				$res = $sth->fetchAll();

			} else {
				$sql = "SELECT temperature FROM " . $this->table . " ORDER BY created_at DESC LIMIT 1";
				$sth = $dbh->prepare($sql);
				$sth->execute();
				$res = $sth->fetchAll();
			}

			echo json_encode($res);
		} catch (\Exception $e) {
			error_log($e->getMessage());
			echo "There is an error while fetching the data.";
		}
	}
}
