<?php
class Dice {
    /**
     * @var int $topSideVal
     */
    private $topSideVal;

    /**
     * @return int
     */
    public function getTopSideVal()
    {
        return $this->topSideVal;
    }

    /**
     * @return int
     */
    public function roll()
    {
        $this->topSideVal =  rand(1,6);
        return $this;
    }

    /**
     * @param int $topSideVal
     * @return Dice
     */
    public function setTopSideVal($topSideVal)
    {
        $this->topSideVal = $topSideVal;
        return $this;
    }
}


class Player
{

    /** 
     * @var array $diceInCup
     */
    private $diceInCup = [];

    /** 
     * @var string $name
     */
    private $name;

    /**
     * @var int $position
     */
    private $position;

    /**
     * @var int $point
     */
    private $point;

    /**
     * @return array
     */

     //function untuk mengambil jumlah dadu
    public function getDiceInCup()
    {
        return $this->diceInCup;
    }

    /**
     * @return string
     */

     //function ini berfungsi untuk mengambil name dari pemain
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */

     //function ini berfungsi untuk mengambil posisi data pemain setiap putaran
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Player constructor.
     * @param int $numberOfDice
     */

     //function ini berfungsi untuk mengambil posisi nama dan nomor dadu
    public function __construct($numberOfDice, $position, $name = '')
    {
        /* Set point to 0 */
        $this->point = 0;

        /* position 0 is the left most */
        $this->position = $position;

        /* Optional name, example Player A */
        $this->name = $name;

        /* Initialize array of dice */
        for ($i = 0; $i < $numberOfDice; $i++) {
            array_push($this->diceInCup, new Dice());
        }
    }
    
    /**
     * Add point
     * 
     * @var int $point
     */

     //function ini berfungsi untuk menambahkan jumlah point pemain
    public function addPoint($point)
    {
        $this->point += $point;
    }

    /**
     * Get point
     * 
     * @return int
     */

     //function ini untuk mengambil data point
    public function getPoint()
    {
        return $this->point;
    }

    //function ini untuk action pemutaran dadu
    public function play()
    {
        foreach($this->diceInCup as $dice){
            $dice->roll();
        }
    }

    /**
     * @param int $key
     */

     //function ini berfungsi untuk mengevaluasi dadu
    public function removeDice($key)
    {
        unset($this->diceInCup[$key]);
    }

    /**
     * @param Dice $dice
     */

     //function ini berfungsi untuk memasukan jumlah dadu
    public function insertDice($dice)
    {
        array_push($this->diceInCup, $dice);
    }
}

/**
 * Class Game
 */
class Game
{
    /**
     * @var array $players = []
     */
    private $players = [];

    /**
     * @var int $round
     */
    private $round;

    /**
     * @var int $numberOfPlayer
     */
    private $numberOfPlayer;
    
    /**
     * @var int $numberOfDicePerPlayer
     */
    private $numberOfDicePerPlayer;

    const REMOVED_WHEN_DICE_TOP = 6;
    const MOVE_WHEN_DICE_TOP = 1;

    
    public function __construct($numberOfPlayer, $numberOfDicePerPlayer)
    {
        $this->round = 0;
        $this->numberOfPlayer = $numberOfPlayer;
        $this->numberOfDicePerPlayer = $numberOfDicePerPlayer;

        /* The game contains players and each player have dices */
        for ($i = 0; $i < $this->numberOfPlayer; $i++) {
            $this->players[$i] = new Player($this->numberOfDicePerPlayer, $i, chr(65 + $i));
        }
    }

    /**
     * Display round.
     * 
     * @return $this
     */

     //function ini berfungsi untuk menampilkan ronde putaran pemain
    private function displayRound()
    {
        echo "<strong>Putaran ke- {$this->round}</strong><br/>\r\n";
        return $this;
    }

    /**
     * Show top side dice
     *
     * @param string $title
     * @return $this
     */

     //function ini befungsi menampilkan hasil lemparan dadu dari pemain
    private function displayTopSideDice($title = 'Lempar Dadu')
    {
        echo "<span>{$title}:</span><br/>";
        foreach ($this->players as $player) {
            echo "Pemain #{$player->getName()}: ";
            $diceTopSide = '';

            foreach ($player->getDiceInCup() as $dice) {
                $diceTopSide .= $dice->getTopSideVal() . ", ";
            }

            
            echo rtrim($diceTopSide, ',') . "<br/>\r\n";
        }

        echo "<br/>\r\n";
        return $this;
    }

    /**
     * @param Player $player
     * @return $this
     */

     //function ini menampilkan pemenang dan nilai akhir dari permainan
    public function displayWinner($player)
    {
        echo "<h1>Pemenang</h1>\r\n";
        echo "Pemain {$player->getName()}<br>\r\n";
        echo "Nilai : {$player->getPoint()}<br>\r\n";

        return $this;
    }

    /**
     * session untuk memulai permainan dadu
     */

     //function ini sebagai function utama untuk memulai pemainan dengan memasukan jumlah dadu dan jumlah pemain
    public function start()
    {
        echo "Jumlah Pemain = {$this->numberOfPlayer}, Jumlah Dadu = {$this->numberOfDicePerPlayer}<br/><br/>\r\n";
        // Loop sampai menemukan pemenang
        while (true) {
            $this->round++;
            $diceCarryForward = [];

            foreach ($this->players as $player) {
                $player->play();
            }

         
            $this->displayRound()->displayTopSideDice();

            /* untuk mengecheck dadu pemain yang ada di permainan */
            foreach ($this->players as $index => $player) {
                $tempDiceArray = [];

                foreach ($player->getDiceInCup() as $diceIndex => $dice) {
                    //untuk mengecheck apakah ada pemain yang mendapatkan dadu bernilai 6
                    if ($dice->getTopSideVal() == self::REMOVED_WHEN_DICE_TOP) {
                        $player->addPoint(1);
                        $player->removeDice($diceIndex);
                    }

                    //untuk mengecheck apakah ada pemain yang mendapatkan dadu bernilai 1
                    if ($dice->getTopSideVal() == self::MOVE_WHEN_DICE_TOP) {
                       
                        if ($player->getPosition() == ($this->numberOfPlayer - 1)) {
                            $this->players[0]->insertDice($dice);
                            $player->removeDice($diceIndex);
                        } else {
                            array_push($tempDiceArray, $dice);
                            $player->removeDice($diceIndex);
                        }
                    }
                }

                $diceCarryForward[$index + 1] = $tempDiceArray;

                if (array_key_exists($index, $diceCarryForward) && count($diceCarryForward[$index]) > 0) {
                    // Insert the dice
                    foreach ($diceCarryForward[$index] as $dice) {
                        $player->insertDice($dice);
                    }

                    // Reset
                    $diceCarryForward = [];
                }
            }

            //menampilkan hasil evaluasi
            $this->displayTopSideDice("Hasil Evaluasi");

           //mengechech pemain yang memiliki dadu
            $playerHasDice = $this->numberOfPlayer;

            foreach ($this->players as $player) {
                if (count($player->getDiceInCup()) <= 0) {
                    $playerHasDice--;
                }
            }

            //mengecheck pemain yang hanya memiliki 1 buah dadu
            if ($playerHasDice == 1) {
                $this->displayWinner($this->getWinner());
                /* Exit the loop */
                break;
            }
        }
    }

    /**
     * Get winner
     *
     * @return Player
     */

     //function ini berfungsi untuk mencari pemenang dari permainan dengan mengambil point dari setiap pemain
    private function getWinner()
    {
        $winner = null;
        $highscore = 0;
        foreach ($this->players as $player) {
            if ($player->getPoint() > $highscore) {
                $highscore = $player->getPoint();
                $winner = $player;
            }
        }

        return $winner;
    }
}

//untuk memasukan jumlah dadu dan jumlah pemain
$game = new Game(3, 3);

//untuk memulai permainan
$game->start();