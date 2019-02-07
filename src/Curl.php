<?php
/**
 * Fichier Curl.php du 06/02/2019
 * Description : Fichier de la classe Curl
 *
 * PHP version 5
 *
 * @category API
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @link     https://github.com/rcnchris On Github
 */

namespace Rcnchris\Common;

/**
 * Class Curl
 * <ul>
 * <li>Communiquer avec différents types de serveurs</li>
 * </ul>
 *
 * @category API
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @version  Release: <1.0.0>
 *
 * @link     https://github.com/rcnchris on Github
 */
class Curl
{

    /**
     * Aide de cette classe
     *
     * @var array
     */
    private $help = [
        "Communiquer avec différents types de serveurs"
    ];

    /**
     * Session cURL
     *
     * @var resource
     */
    private $curl;

    /**
     * Options de transmissions par défaut de cURL
     *
     * @var array
     */
    private $defaultOptions = [
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_CRLF => true,
        CURLOPT_FAILONERROR => true,
        CURLOPT_FORBID_REUSE => false,
        CURLOPT_FRESH_CONNECT => false,
        CURLOPT_FTP_USE_EPRT => false,
        CURLOPT_FTP_USE_EPSV => true,
        CURLOPT_HEADER => false,
        CURLOPT_POST => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        //CURLOPT_SSLVERSION => 'CURL_SSLVERSION_TLSv1_2'
        CURLOPT_TIMEOUT => 4,
        CURLOPT_UPLOAD => false,
    ];

    /**
     * Contenu du retour de curl_exec
     *
     * @var mixed
     */
    private $response;

    /**
     * URL donnée à la construction de l'instance
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Tableau des requêtes exécutées
     *
     * @var array
     */
    private $logs = [];

    /**
     * Constructeur
     *
     * @param string|null $url     URL à contacter
     * @param array|null  $options Options de Curl
     */
    public function __construct($url = null, array $options = [])
    {
        $this->curl = curl_init($url);
        $this->setBaseUrl((string)$this);
        $this->setOptions($this->defaultOptions);
    }

    /**
     * Fermeture de la session cURL
     *
     * @see http://php.net/manual/fr/function.curl-close.php
     */
    public function __destruct()
    {
        if (!is_null($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * Obtenir l'URL de cURL
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getInfos('url');
    }

    /**
     * Définir une ou plusieurs options de transmission de cURL
     *
     * ### Example
     * - `$url->setOptions(CURLOPT_AUTOREFERER, false);`
     * - `$url->setOptions([CURLOPT_AUTOREFERER => false, CURLOPT_RETURNTRANSFER => false]);`
     *
     * @param array|string $option Nom de l'option à définir ou tableau options/valeur
     * @param mixed|null   $value  Valeur dans le cas où l'option est passée en premier paramètre
     *
     * @return self
     *
     * @see http://php.net/manual/fr/function.curl-setopt.php
     * @see http://php.net/manual/fr/function.curl-setopt-array.php
     */
    public function setOptions($option, $value = null)
    {
        is_array($option)
            ? curl_setopt_array($this->curl, $option)
            : curl_setopt($this->curl, $option, $value);
        return $this;
    }

    /**
     * Obtenir l'URL de base
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Obtenir l'URL de cURL
     *
     * @param bool|null $decode Retourne l'URL décodée
     *
     * @return string
     */
    public function getUrl($decode = false)
    {
        $url = (string)$this;
        if ($decode) {
            return rawurldecode($url);
        }
        return $url;
    }

    /**
     * Définir l'URL de base
     *
     * @param string $url URL de base
     *
     * @return self
     */
    public function setBaseUrl($url = null)
    {
        if (is_null($url)) {
            $url = $this->getBaseUrl();
        }
        if ($this->isUrl($url)) {
            $this->baseUrl = $url;
        }
        return $this;
    }

    /**
     * Vérifie si la chaîne correspond à une syntaxe d'URL valide
     *
     * @param string $url URL à vérifier
     *
     * @return bool
     *
     * @see http://php.net/manual/fr/function.filter-var.php
     */
    private function isUrl($url)
    {
        return $url === filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Obtenir les paramètres de la requête cURL
     *
     * @param bool|null $toString Retourne une chaîne de caractères décodée plutot qu'un objet
     *
     * @return bool|array|string
     */
    public function getParams($toString = false)
    {
        if ($this->parseUrl('query')) {
            if ($toString) {
                return rawurldecode($this->parseUrl('query'));
            }
            $query = $this->parseUrl('query');
            $items = explode('&', $query);
            $params = [];
            foreach ($items as $param) {
                list($key, $value) = explode('=', $param);
                $params[$key] = rawurldecode($value);
            }
            return $params;
        }
        return false;
    }

    /**
     * Obtenir les informations détaillant un transfert cURL
     *
     * @param string|null $key Nom d'une clé
     *
     * @return array|mixed
     *
     * @see http://php.net/manual/fr/function.curl-getinfo.php
     */
    public function getInfos($key = null)
    {
        $infos = curl_getinfo($this->curl);
        if (array_key_exists($key, $infos)) {
            return $infos[$key];
        }
        return $infos;
    }

    /**
     * Exécute la requête de l'URL courante, stocke la réponse, la trace et retourne la réponse
     *
     * @param string|null $title Titre de la requête pour le journal
     *
     * @return mixed
     */
    public function exec($title = null)
    {
        $this->response = curl_exec($this->curl);
        $this->log($title);
        return $this->getResponse();
    }

    /**
     * Ajoute des parties à l'URL courante
     *
     * @param array|string $parts Partie(s) à ajouter à l'URL
     *
     * @return self
     * @throws \Exception
     */
    public function withParts($parts)
    {
        $this->setUrl();
        $url = $this->getUrl();
        if (is_string($parts)) {
            $url .= '/' . trim($parts, '/');
        } elseif (is_array($parts)) {
            $url .= '/' . implode('/', $parts);
        }
        $this->setUrl($url);
        return $this;
    }

    /**
     * Définir le navigateur de la requête
     *
     * @param string $browser Nom du navigateur
     *
     * @return self
     */
    public function withUserAgent($browser)
    {
        return $this->setOptions(CURLOPT_USERAGENT, $browser);
    }

    /**
     * Ajoute des paramètres à l'URL
     *
     * @param array     $params
     *
     * @param bool|null $erase Supprime les paramètres existants
     *
     * @return self
     */
    public function withParams(array $params = [], $erase = false)
    {
        if ($erase) {
            $this->setUrl($this->getUrl());
        }
        $this->setUrl($this->getUrl() . '?' . http_build_query($params));
        return $this;
    }

    /**
     * Définir l'URL de cURL
     *
     * @param string $url Nouvelle URL de cURL
     *
     * @return self|bool
     */
    public function setUrl($url = null)
    {
        if (is_null($url)) {
            $url = $this->getBaseUrl();
        }
        if ($this->isUrl($url)) {
            if ($this->getUrl() === '') {
                $this->setBaseUrl($url);
            }
            $this->setOptions(CURLOPT_URL, $url);
            return $this;
        }
        return false;
    }

    /**
     * Obtenir les parties de l'URL dans un objet ou la valeur d'une clé
     *
     * ### Example
     * - `$api->parseUrl()->toArray();`
     * - `$api->parseUrl('host');`
     *
     * @param string|null $key Clé à retourner
     *
     * @return mixed
     *
     * @see http://php.net/manual/fr/function.parse-url.php
     */
    public function parseUrl($key = null)
    {
        $parts = parse_url((string)$this);
        if (is_null($key)) {
            return $parts;
        }
        if (array_key_exists($key, $parts)) {
            return $parts[$key];
        }
        return null;
    }

    /**
     * Ajoute la dernière requête au journal
     *
     * @param string|null $title Titre de la requête
     */
    private function log($title = null)
    {
        array_push($this->logs, [
            'class' => get_class($this),
            'title' => $title,
            'details' => $this->getInfos()
        ]);
    }

    /**
     * Obtenir le journal des requêtes exécutées
     *
     * @return array
     */
    public function getLog()
    {
        return $this->logs;
    }

    /**
     * Obtenir cURL
     *
     * @return resource
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * Obtenir l'aide de cette classe
     *
     * @param bool|null $text Si faux, c'est le tableau qui ets retourné
     *
     * @return array|string
     */
    public function help($text = true)
    {
        if ($text) {
            return join('. ', $this->help);
        }
        return $this->help;
    }

    /**
     * Obtenir la réponse
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Obtenir l'URL courante de l'API
     *
     * @return string|null
     */
    public function getContentType()
    {
        $contentType = explode(';', $this->getInfos('content_type'));
        return current($contentType);
    }

    /**
     * Obtenir la réponse dans un objet selon le content-type de la réponse
     *
     * @return mixed|null|\Rcnchris\Common\Image|\Rcnchris\Common\Items|\SimpleXMLElement
     */
    public function toObject()
    {
        $type = $this->getContentType();
        $o = null;
        $errors = [
            'code' => $this->getInfos('http_code'),
            'type' => $type,
            'infos' => $this->getInfos(),
            'response' => $this->response,
            'curlError' => curl_error($this->curl),
            'curlErrorCode' => curl_errno($this->curl)
        ];
        if ($type === 'text/html') {
            $o = $this->response;
        } elseif ($type === 'application/json') {
            $content = json_decode($this->response, true);
            $o = is_array($content)
                ? new Items($content)
                : new Items(array_merge(
                    $errors,
                    ['jsonError' => json_last_error(), 'jsonErrorMsg' => json_last_error_msg()]
                ));
        } elseif ($type === 'text/csv') {
            // Chaque ligne dans un tableau
            $array = str_getcsv($this->response, "\n");

            // Extraction des entêtes de colonnes
            $headers = str_getcsv($array[0]);
            unset($array[0]);

            // Traitement des lignes
            $rows = [];
            foreach ($array as $indLine => $row) {
                foreach (str_getcsv($row) as $indCol => $value) {
                    if (isset($headers[$indCol])) {
                        $rows[$indLine][$headers[$indCol]] = $value;
                    }
                }
            }
            $o = new Items($rows);
        } elseif (in_array($type, ['application/xml', 'text/xml'])) {
            libxml_use_internal_errors(true);
            $o = simplexml_load_string($this->response);
            if ($o === false) {
                $xmlErrors = [];
                foreach (libxml_get_errors() as $e) {
                    $xmlErrors[] = [
                        'code' => $e->code,
                        'line' => $e->line,
                        'column' => $e->column,
                        'msg' => $e->message,
                    ];
                }
                $o = new Items(array_merge($errors, compact('xmlErrors')));
            }
        } elseif (in_array($type, ['image/jpeg'])) {
            $o = new Image($this->getUrl());
        }
        return $o;
    }
}
