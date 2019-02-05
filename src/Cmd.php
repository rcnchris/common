<?php
/**
 * Fichier Cmd.php du 26/01/2019
 * Description : Fichier de la classe Cmd
 *
 * PHP version 5
 *
 * @category Shell
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @link     https://github.com/rcnchris/common/Cmd On Github
 */

namespace Rcnchris\Common;

/**
 * Class Cmd
 * <ul>
 * <li>Exécute une ou plusieurs commandes <code>shell</code></li>
 * <li>Le résultat est retourné dans un tableau s'il contient plusieurs lignes</li>
 * </ul>
 *
 * @category Shell
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @version  Release: <1.0.0>
 * @since    Release: <1.0.0>
 *
 * @link     https://github.com/rcnchris/common/Cmd on Github
 */
class Cmd
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Permet d'exécuter une ou plusieurs <strong>commandes shell</strong>"
    ];

    /**
     * Liste des commandes
     *
     * @var array
     */
    private $commands = [];

    /**
     * Constructeur
     *
     * @param string|null $commands Commande à exécuter
     */
    public function __construct($commands = null)
    {
        $this->add($commands);
    }

    /**
     * Ajoute une commande au tableau des commandes à exécuter
     *
     * @param string $command Commande shell
     *
     * @return self
     */
    public function add($command)
    {
        if (!is_null($command) && is_string($command) && $command != '') {
            $commands = explode(' && ', $command);
            foreach ($commands as $command) {
                $this->commands[] = ['cmd' => $command];
            }
        }
        return $this;
    }

    /**
     * Retourne la liste des commandes à exécuter dans une instance de <code>Items</code>,
     * ou si une seule commande la commande au format texte
     *
     * @return string|null|\Rcnchris\Common\Items
     */
    public function getCommands()
    {
        return count($this->commands) === 1 ? $this->commands[0]['cmd'] : $this->commands;
    }

    /**
     * Exécute toutes les commandes de l'instance
     *
     * @param bool|null $withInfos Si vrai, les information d'exécution ssont retournées en plus des résultats
     *
     * @return array|string Tableau de retour contenant le contexte d'exécution,
     * le résultat et le code retour d'exécution
     * ou une chaîne contenant le résultat de la commande
     */
    public function exec($withInfos = false)
    {
        $codeRet = 0;
        $ret = null;
        $res = null;

        // Pour chaque commande à exécuter
        // Je l'exécute et stocke les informations de retour
        // dans le tableau des commandes
        foreach ($this->commands as $k => $command) {
            exec($command['cmd'], $this->commands[$k]['result'], $codeRet);
            if ($codeRet != 0) {
                // Embrouille lors de l'exécution de la commande,
                // Exécution de la commande avec popen
                // pour récupérer le message d'erreur
                $handle = popen($command['cmd'] . ' 2>&1', 'r');
                $res = fread($handle, 2096);
                $this->commands[$k]['result'] = $res;
                pclose($handle);
            }

            // Préparation du résultat
            if (empty($this->commands[$k]['result'])) {
                $this->commands[$k]['result'] = null;
            } elseif (is_array($this->commands[$k]['result'])
                && count($this->commands[$k]['result']) === 1
            ) {
                $this->commands[$k]['result'] = $this->commands[$k]['result'][0];
            }

            // Contexte d'exécution
            $this->commands[$k]['time'] = microtime(true);
            $this->commands[$k]['ret'] = $codeRet;
        }

        // Traitement du retour
        // Si je n'ai qu'une seule commande
        // et/ou que le résultat tient en une ligne,
        // je retourne une chaîne, sinon un tableau avec uniquement les résultats
        return count($this->commands) === 1
            ? ($withInfos ? current($this->commands) : current($this->commands)['result'])
            : ($withInfos ? $this->commands : array_column($this->commands, 'result'));
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
        return $text ? join('. ', self::$help) : self::$help;
    }
}
