<?php

namespace App\Http\Controllers\Enums;

enum LoadTarget {
    case Initialize;

    case Timeline;
    case Player;
    case ActiveUser;

    case Account;
    case Chat;
    case Datastore;
    case Dictionary;
    case Exchange;
    case Experience;
    case Friend;
    case Inbox;
    case Inventory;
    case JobQueue;
    case Limit;
    case Lottery;
    case Matchmaking;
    case Mission;
    case Money;
    case Quest;
    case Ranking;
    case Realtime;
    case Schedule;
    case Script;
    case Showcase;
    case Stamina;
    case Version;

    case Done;

    public static function valueOf(string $value) {
        return match ($value) {
            "Timeline" => self::Timeline,
            "Player" => self::Player,
            "ActiveUser" => self::ActiveUser,

            "Account" => self::Account,
            "Chat" => self::Chat,
            "Datastore" => self::Datastore,
            "Dictionary" => self::Dictionary,
            "Exchange" => self::Exchange,
            "Experience" => self::Experience,
            "Friend" => self::Friend,
            "Inbox" => self::Inbox,
            "Inventory" => self::Inventory,
            "JobQueue" => self::JobQueue,
            "Limit" => self::Limit,
            "Lottery" => self::Lottery,
            "Matchmaking" => self::Matchmaking,
            "Mission" => self::Mission,
            "Money" => self::Money,
            "Quest" => self::Quest,
            "Ranking" => self::Ranking,
            "Realtime" => self::Realtime,
            "Schedule" => self::Schedule,
            "Script" => self::Script,
            "Showcase" => self::Showcase,
            "Stamina" => self::Stamina,
            "Version" => self::Version,

            "Done" => self::Done,

            default => self::Initialize,
        };
    }

    public function toString(): string {
        return match ($this) {
            self::Timeline => "Timeline",
            self::Player => "Player",
            self::ActiveUser => "ActiveUser",


            self::Account => "Account",
            self::Chat => "Chat",
            self::Datastore => "Datastore",
            self::Dictionary => "Dictionary",
            self::Exchange => "Exchange",
            self::Experience => "Experience",
            self::Friend => "Friend",
            self::Inbox => "Inbox",
            self::Inventory => "Inventory",
            self::JobQueue => "JobQueue",
            self::Limit => "Limit",
            self::Lottery => "Lottery",
            self::Matchmaking => "Matchmaking",
            self::Mission => "Mission",
            self::Money => "Money",
            self::Quest => "Quest",
            self::Ranking => "Ranking",
            self::Realtime => "Realtime",
            self::Schedule => "Schedule",
            self::Script => "Script",
            self::Showcase => "Showcase",
            self::Stamina => "Stamina",
            self::Version => "Version",

            self::Done => "Done",

            default => "Initialize",
        };
    }
}
