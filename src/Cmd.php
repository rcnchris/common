<?php
/**
 * Fichier Cmd.php du 26/01/2019
 * Description : Fichier de la classe Cmd
 *
 * PHP version 5
 *
 * @category New
 *
 * @package Rcnchris\Common
 *
 * @author Raoul <rcn.chris@gmail.com>
 *
 * @license https://github.com/rcnchris GPL
 *
 * @link https://github.com/rcnchris On Github
 */

namespace Rcnchris\Common;

/**
 * Class Cmd
 *
 * @category New
 *
 * @package Rcnchris\Common
 *
 * @author Raoul <rcn.chris@gmail.com>
 *
 * @license https://github.com/rcnchris GPL
 *
 * @version Release: <1.0.0>
 *
 * @link https://github.com/rcnchris on Github
 */
class Cmd
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Permet d'exécuter des <strong>commandes shell</strong>",
        'Instanciable et statique',
    ];

    /**
     * Liste des commandes
     *
     * @var array
     */
    private static $cmds = [];

    /**
     * Instance
     *
     * @var $this
     */
    private static $instance;

    /**
     * Obtenir une instance (Singleton)
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
     * Exécute une ou plusieurs commandes et retourne le résultat
     *
     * @param string    $cmd      La ou les commandes (séparées par des " && ")
     *                            à exécuter
     * @param bool|null $separate Sépare les commandes en commandes uniques
     *                            si les caractères " && " figurent dans la chaîne
     *
     * @return array|string Tableau de retour contenant le contexte d'exécution,
     * le résultat et le code retour d'exécution
     * ou une chaîne contenant le résultat de la commande
     */
    public static function exec($cmd, $separate = false)
    {
        $codeRet = 0;
        $ret = null;
        $res = null;

        // Ajout des commandes
        // ou de la commande à la liste des commandes à exécuter
        self::addCmd($cmd, true, $separate);

        // Pour chaque commande à exécuter
        // Je l'exécute et stocke les informations de retour
        // dans le tableau des commandes
        foreach (self::$cmds as $k => $command) {
            exec($command['cmd'], self::$cmds[$k]['result'], $codeRet);
            if ($codeRet != 0) {
                // Embrouille lors de l'exécution de la commande,
                // Exécution de la commande avec popen
                // pour récupérer le message d'erreur
                $handle = popen($command['cmd'] . ' 2>&1', 'r');
                $res = fread($handle, 2096);
                self::$cmds[$k]['result'] = $res;
                pclose($handle);
            }

            // Préparation du résultat
            if (empty(self::$cmds[$k]['result'])) {
                self::$cmds[$k]['result'] = null;
            } elseif (is_array(self::$cmds[$k]['result'])
                && count(self::$cmds[$k]['result']) === 1
            ) {
                self::$cmds[$k]['result'] = self::$cmds[$k]['result'][0];
            }

            // Contexte d'exécution
            self::$cmds[$k]['time'] = date('Y-m-d H:i:s');
            self::$cmds[$k]['ret'] = $codeRet;
        }

        // Traitement du retour
        // Si je n'ai qu'une seule commande
        // et/ou que le résultat tient en une ligne,
        // je retourne une chaîne, sinon un tableau avec uniquement les résultats
        return count(self::$cmds) === 1
            ? self::$cmds[0]['result']
            : self::getCmds(true);
    }

    /**
     * Setter : Ajoute une commande
     * ou liste de commandes au tableau des commandes à exécuter
     *
     * @param string    $cmd      Commande
     * @param bool|null $erase    Efface les commandes précédentes
     * @param bool|null $separate Sépare les commandes
     *
     * @return bool
     */
    private static function addCmd($cmd, $erase = false, $separate = false)
    {
        if ($erase) {
            self::$cmds = [];
        }

        if ($separate) {
            $cmds = explode(' && ', $cmd);
            foreach ($cmds as $command) {
                self::$cmds[] = [
                    'cmd' => $command,
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        } else {
            self::$cmds[] = [
                'cmd' => $cmd,
                'time' => date('Y-m-d H:i:s')
            ];
        }

        return true;
    }

    /**
     * Getter : Retourne la liste des commandes à exécuter
     *
     * @param bool|null $onlyResult Retourne les résultats sans les commandes
     *
     * @return array
     */
    public static function getCmds($onlyResult = false)
    {
        if ($onlyResult) {
            $ret = [];
            foreach (self::$cmds as $k => $command) {
                $ret[$k] = $command['result'];
            }
            return $ret;
        } else {
            return self::$cmds;
        }
    }

    /**
     * Utilisation de la commande "git"
     *
     * @param string|null $option Option de la commande
     *
     * @return array|string
     */
    public static function git($option = 'version')
    {
        $options = ['version', 'help', 'info-path', 'man-path', 'html-path'];
        if (in_array($option, $options)) {
            $option = "--" . $option;
        }
        $cmd = "git " . $option;
        return self::exec($cmd);
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
