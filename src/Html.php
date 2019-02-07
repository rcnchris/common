<?php
/**
 * Fichier Html.php du 13/07/2018
 * Description : Fichier de la classe Html
 *
 * PHP version 5
 *
 * @category HTML
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
 * Class Html
 * <ul>
 * <li>Génération de balises HTML</li>
 * </ul>
 *
 * @category HTML
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @version  Release: <1.0.0>
 * @since    Release: <1.0.5>
 *
 * @link     https://github.com/rcnchris on Github
 */
class Html
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        'Helper HTML',
        'Génération de balises HTML'
    ];

    /**
     * Instance de cette classe
     *
     * @var self
     */
    private static $instance;

    /**
     * Obtenir l'instance de cette classe
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Génère un lien
     *
     * @param string      $link       Lien
     * @param string|null $label      Label du lien
     * @param array|null  $attributes Attributs du lien
     *
     * @return string
     */
    public static function link($link, $label = null, array $attributes = [])
    {
        $attributes = array_merge($attributes, ['href' => $link]);
        if (is_null($label)) {
            $label = $link;
        }
        return self::surround($label, 'a', $attributes);
    }

    /**
     * Obtenir une balise `img`
     *
     * @param string     $src        Chemin de l'image
     * @param array|null $attributes Attributs de la balise `img`
     *
     * @return string
     */
    public static function img($src, array $attributes = [])
    {
        $attributes = array_merge($attributes, ['src' => $src]);
        return '<img' . self::parseAttributes($attributes) . '>';
    }

    /**
     * Générer une liste `ul`, `ol` ou `dl`
     *
     * @param mixed      $items      Liste des items à lister
     * @param array|null $attributes Attributs de la liste
     * @param bool|null  $withKeys   La valeur est précédée de sa clé
     *
     * @return string
     */
    public static function liste($items, array $attributes = ['type' => 'ul'], $withKeys = false)
    {
        $type = $attributes['type'];
        unset($attributes['type']);

        $html = '';
        if ($type !== 'dl') {
            foreach ($items as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $html = $withKeys
                        ? $html . self::surround($key . ' : ' . $value, 'li')
                        : $html . self::surround($value, 'li');
                } elseif (is_object($value)) {
                    $html = $withKeys
                        ? $html . self::surround($key . ' : ' . get_class($value), 'li')
                        : $html . self::surround(get_class($value), 'li');
                } elseif (is_array($value) && !is_array($key)) {
                    $html = $withKeys
                        ? $html . self::surround($key . ' : ' . implode(', ', array_values($value)), 'li')
                        : $html . self::surround(implode(', ', array_values($value)), 'li');
                } elseif (is_resource($value)) {
                    $html = $withKeys
                        ? $html . self::surround($key . ' : ' . get_resource_type($value), 'li')
                        : $html . self::surround(get_resource_type($value), 'li');
                }
            }
        } else {
            foreach ($items as $key => $value) {
                $html .= self::surround($key, 'dt') . self::surround($value, 'dd');
            }
        }
        return self::surround($html, $type);
    }

    /**
     * Générer une balise `details`
     *
     * @param string     $title      Titre
     * @param string     $content    Contenu
     * @param array|null $attributes Attributs de la balise `details`
     *
     * @return string
     */
    public static function details($title, $content, array $attributes = [])
    {
        return self::surround(self::surround($title, 'summary') . $content, 'details', $attributes);
    }

    /**
     * Entourre le contenu d'une balise
     *
     * @param string     $content    Contenu à entourrer
     * @param string     $tag        Balise HTML
     * @param array|null $attributes Attributs de la balise qui entourre
     *
     * @return string
     */
    public static function surround($content, $tag, array $attributes = [])
    {
        if (!is_string($tag) || is_array($content)) {
            return null;
        }
        return '<' . $tag . self::parseAttributes($attributes) . '>' . $content . '</' . $tag . '>';
    }

    /**
     * Obtenir un tableau HTML à partir d'une liste
     * - `$html->table($items);`
     * - `$html->table($items, ['class' => 'table table-sm']);`
     * - `$html->table($items, ['caption' => 'Un titre']);`
     * - `$html->table($items, [], false);`
     *
     * @param mixed      $items      Liste
     * @param array|null $attributes Attributs du tableau
     * @param bool|null  $withHeader fficher les entêtes
     * @param bool|null  $colMode    Lite ou présentation (list, presentation)
     *
     * @return string
     */
    public static function table($items, array $attributes = [], $withHeader = true, $colMode = false)
    {
        // Caption
        $caption = null;
        if (array_key_exists('caption', $attributes)) {
            $caption = self::surround($attributes['caption'], 'caption');
            unset($attributes['caption']);
        }

        // Attributes
        $attributes = self::parseAttributes($attributes);

        $html = '<table' . $attributes . '>';
        $html .= $caption;

        // Header
        if ($colMode) {
            $html .= '<thead><tr>';
            foreach ($items as $key => $value) {
                $html .= self::surround($key, 'th');
            }
            $html .= '</tr></thead>';
        }

        // Body
        $html .= '<tbody>';
        if ($colMode) {
            $html .= '<tr>';
            foreach ($items as $key => $value) {
                if (is_array($value) || $value instanceof \Traversable) {
                    $html .= self::surround(self::table($value, [], $withHeader, $colMode), 'td');
                } elseif ($value instanceof \DateTime) {
                    $html .= self::surround($value->format('d-m-Y H:i:s'), 'td');
                } elseif (is_object($value)) {
                    $html = method_exists($value, '__toString')
                        ? $html . self::surround((string)$value, 'td')
                        : $html . self::surround(get_class($value), 'td');
                } else {
                    $html .= self::surround($value, 'td');
                }
            }
            $html .= '</tr>';
        } else {
            foreach ($items as $key => $value) {
                $html .= '<tr>';
                if ($withHeader) {
                    $html .= self::surround($key, 'th');
                }
                if (is_array($value) || $value instanceof \Traversable) {
                    $html .= self::surround(self::table($value, [], $withHeader, $colMode), 'td');
                } elseif ($value instanceof \DateTime) {
                    $html .= self::surround($value->format('d-m-Y H:i:s'), 'td');
                } elseif (is_object($value)) {
                    $html = method_exists($value, '__toString')
                        ? $html . self::surround((string)$value, 'td')
                        : $html . self::surround(get_class($value), 'td');
                } else {
                    $html .= self::surround($value, 'td');
                }
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        return $html;
    }

    /**
     * Obtenir une balise d'icône
     *
     * ### Example
     * - `$html->icon('home');`
     *
     * @param string      $name Nom de l'icône
     * @param string|null $lib  Librairie à utiliser
     *
     * @return string
     */
    public function icon($name, $lib = 'fa')
    {
        if ($lib === 'fa') {
            return '<i class="fa fa-' . $name . '"></i>';
        }
        return '<span class="' . $name . '"></span>';
    }

    /**
     * Obtenir une balise `input`
     *
     * @param array|null $attributes
     *
     * @return string
     */
    public static function input(array $attributes = [])
    {
        return '<input' . self::parseAttributes($attributes) . '>';
    }

    /**
     * Obtenir les attributs d'une balise HTML dans une chaîne de caractères
     *
     * @param array $attributes
     *
     * @return string
     */
    public static function parseAttributes(array $attributes)
    {
        $attr = [];
        $singleAttributes = ['checked', 'selected', 'multiple', 'required', 'disabled'];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $singleAttributes)) {
                $attr[] = $key;
            } else {
                $attr[] = $key . '="' . $value . '"';
            }
        }
        sort($attr);
        return !empty($attr)
            ? ' ' . implode(' ', $attr)
            : null;
    }

    /**
     * Obtenir la balise html qui correspond au type demandé
     *
     * @param string     $name    Nom de la balise générée
     * @param mixed|null $value   Valeur actuelle
     * @param array|null $options Options de la balise à générer
     *
     * @return null|string
     */
    public static function field($name, $value = null, array $options = [])
    {
        $html = null;
        $type = isset($options['type']) ? $options['type'] : 'text';
        if (!is_null($value)) {
            $value = self::toStr($value);
        }
        $attributes = [
            'name' => $name,
            'id' => $name,
            'type' => $type,
            'value' => $value
        ];
        if (array_key_exists('disabled', $options)) {
            $attributes['disabled'] = true;
        }
        if (array_key_exists('required', $options)) {
            $attributes['required'] = true;
        }
        if (array_key_exists('placeholder', $options)) {
            $attributes['placeholder'] = $options['placeholder'];
        }
        if (array_key_exists('class', $options)) {
            $attributes['class'] = $options['class'];
        }

        if ($type === 'textarea') {
            unset($attributes['value']);
            unset($attributes['type']);
            if (array_key_exists('rows', $options)) {
                $attributes['rows'] = $options['rows'];
            }
            if (array_key_exists('cols', $options)) {
                $attributes['cols'] = $options['cols'];
            }
            $html = self::surround($value, 'textarea', $attributes);
        } elseif ($type === 'file') {
            $html = self::input($attributes);
        } elseif ($type === 'checkbox') {
            $html = self::checkbox($attributes['value'], $attributes);
        } elseif ($type === 'radio') {
            $html = self::radio($attributes['value'], $attributes);
        } elseif (array_key_exists('items', $options)) {
            unset($attributes['value']);
            unset($attributes['type']);
            if (array_key_exists('empty', $options)) {
                $attributes['empty'] = true;
            }
            if (array_key_exists('multiple', $options)) {
                $attributes['multiple'] = true;
            }
            $html = self::select($value, $options['items'], $attributes);
        } else {
            $html = self::input($attributes);
        }
        // Label ?
        if (array_key_exists('label', $options)) {
            $html = self::surround($options['label'], 'label', ['for' => $attributes['id']]) . $html;
        }
        return $html;
    }

    /**
     * Obtenir un bouton
     *
     * @param string $label      Texte du bouton
     * @param string $type       Type du bouton (button, submit, reset)
     * @param array  $attributes attrbibut du bouton
     *
     * @return string
     */
    public static function button($label, $type = 'submit', array $attributes = [])
    {
        $attributes['type'] = $type;
        return self::surround($label, 'button', $attributes);
    }

    /**
     * Obtenir un checkbox
     *
     * @param mixed $value      Valeur actuelle
     * @param array $attributes Attibuts de la balise `input`
     *
     * @return string
     */
    private static function checkbox($value, array $attributes = [])
    {
        $hiddenCheck = self::input([
            'type' => 'hidden',
            'name' => $attributes['name'],
            'value' => 0
        ]);
        if ($value) {
            $attributes['checked'] = true;
            unset($attributes['value']);
        }
        $check = self::input($attributes);
        return $hiddenCheck . $check;
    }

    /**
     * Obtenir un bouton radio
     *
     * @param       $value
     * @param array $attributes
     *
     * @return string
     */
    private static function radio($value, array $attributes = [])
    {
        $hiddenCheck = self::input([
            'type' => 'hidden',
            'name' => $attributes['name'],
            'value' => 0
        ]);
        if ($value) {
            $attributes['checked'] = true;
            unset($attributes['value']);
        }
        $radio = self::input($attributes);
        return $hiddenCheck . $radio;
    }

    /**
     * Obtenir une balise `select`
     *
     * @param mixed|null $value      Valeur actuelle
     * @param array      $items      Liste des options
     * @param array      $attributes Attributs de la balise `select`
     *
     * @return string
     */
    private static function select($value = null, array $items = [], array $attributes = [])
    {
        $options = [];
        if (array_key_exists('empty', $attributes)) {
            $options[] = self::surround('', 'option', ['value' => '']);
            unset($attributes['empty']);
        }
        foreach ($items as $k => $v) {
            if (!is_null($value) && $k === intval($value)) {
                $options[] = self::surround($v, 'option', ['value' => $k, 'selected' => true]);
            } else {
                $options[] = self::surround($v, 'option', ['value' => $k]);
            }
        }
        return self::surround(implode('', $options), 'select', $attributes);
    }

    /**
     * Convertir une valeur en chaîne de caractères
     *
     * @param mixed $value Valeur à convertir en chaîne de caractères
     *
     * @return string
     */
    private static function toStr($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        } elseif (is_array($value)) {
            return serialize($value);
        }
        return (string)$value;
    }

    /**
     * Obtenir l'aide de cette classe
     *
     * @param bool|null $text Si faux, c'est le tableau qui ets retourné
     *
     * @return array|string
     */
    public static function help($text = true)
    {
        if ($text) {
            return join('. ', self::$help);
        }
        return self::$help;
    }
}
