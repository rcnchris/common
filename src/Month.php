<?php
/**
 * Fichier Month.php du 14/11/2018
 * Description : Fichier de la classe Month
 *
 * PHP version 5
 *
 * @category Calendrier
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

use Cake\I18n\Time;

/**
 * Class Month
 * <ul>
 * <li>Facilite la manipulation d'un mois dans un calendrier</li>
 * </ul>
 *
 * @category Calendrier
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
class Month
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Facilite la manipulation d'un mois dans un calendrier",
    ];

    /**
     * Tableau des noms de mois par langue
     *
     * @var array
     */
    private $months = [
        'fr' => [
            'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre'
        ]
    ];

    /**
     * Tableau des jours de semaines par langue
     *
     * @var array
     */
    public $days = [
        'fr' => ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche']
    ];

    /**
     * Numéro du mois
     *
     * @var string
     */
    public $month;

    /**
     * Année
     *
     * @var int
     */
    public $year;

    /**
     * Constructeur
     *
     * @param null $month
     * @param null $year
     */
    public function __construct($month = null, $year = null)
    {
        if (is_null($month) || $month < 1 || $month > 12) {
            $month = date('m');
        }
        if (is_null($year)) {
            $year = date('Y');
        }
        $this->month = intval($month);
        $this->year = intval($year);
    }

    /**
     * Obtenir le mois au format string
     *
     * @return string
     */
    public function __toString()
    {
        return ucfirst($this->months['fr'][$this->month - 1]) . ' - ' . $this->year;
    }

    /**
     * Obtenir le nombre de semaines du mois courant
     *
     * @return int
     */
    public function getWeeks()
    {
        return $this->getLastDay()->diffInWeeks($this->getFirstDay());
    }

    /**
     * Obtenir le premier jour du mois
     *
     * @return Time
     */
    public function getFirstDay()
    {
        return Time::createFromFormat('Y-m-d', $this->year . '-' . $this->month . '-01');
    }

    /**
     * Obtenir le dernier jour du mois
     *
     * @return Time
     */
    public function getLastDay()
    {
        return $this->getFirstDay()->endOfMonth();
    }

    /**
     * Vérifie si la date passée en paramètre appartient au mois de l'instance
     *
     * @param \Datetime $date
     *
     * @return bool
     */
    public function inMonth(\Datetime $date)
    {
        return $this->getFirstDay()->format('Y-m') === $date->format('Y-m');
    }


    /**
     * Obtenir le mois suivant
     *
     * @return self
     */
    public function nextMonth()
    {
        $month = $this->month + 1;
        $year = $this->year;
        if ($month > 12) {
            $month = 1;
            $year += 1;
        }
        return new self($month, $year);
    }

    /**
     * Obtenir le mois précedent
     *
     * @return self
     */
    public function previousMonth()
    {
        $month = $this->month - 1;
        $year = $this->year;
        if ($month < 1) {
            $month = 12;
            $year -= 1;
        }
        return new self($month, $year);
    }

    /**
     * Obtenir l'aide de cette classe
     *
     * @param bool|null $text Si faux, c'est le tableau qui est retourné
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
