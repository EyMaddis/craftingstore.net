SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DELIMITER $$
--
-- Funktionen
--
CREATE DEFINER=`mcshop`@`%` FUNCTION `getFailDelay`(FailCount int) RETURNS int(11)
BEGIN
-- Ermittelt zu einer gegebenen Anzahl von fehlgeschlagenen Übertragungsversuchen die Wartezeit.
-- Dabei werden vorherige Wartezeiten aufsummiert.
	RETURN (
		SELECT sum(waitTime)+IF(FailCount>max(count),max(waitTime)*(FailCount-max(count)),0)
		FROM mc_failCountDelay
		WHERE count<=FailCount
		LIMIT 1);
-- RETURN (SELECT sum(waitTime)+IF(FailCount>max(count)) FROM mc_failCountDelay WHERE count<=FailCount LIMIT 1);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `betamails`
--

CREATE TABLE IF NOT EXISTS `betamails` (
`id` int(11) NOT NULL,
  `mail` varchar(255) CHARACTER SET utf8 NOT NULL,
  `invited` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1766 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_boxlayout`
--

CREATE TABLE IF NOT EXISTS `mc_boxlayout` (
`Id` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned DEFAULT NULL,
  `Position` int(11) DEFAULT NULL,
  `Type` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Content` mediumtext COLLATE utf8_unicode_ci,
  `Width` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Visible` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_customcss`
--

CREATE TABLE IF NOT EXISTS `mc_customcss` (
`Id` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned DEFAULT NULL,
  `TemplateId` int(11) DEFAULT NULL,
  `Css` text CHARACTER SET utf8
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_customeraccounts`
--

CREATE TABLE IF NOT EXISTS `mc_customeraccounts` (
`Id` int(11) unsigned NOT NULL,
  `CustomersId` int(11) unsigned DEFAULT NULL,
  `Current` double unsigned DEFAULT NULL COMMENT 'Der aktuelle Kontostand in €-Cent',
  `Difference` double DEFAULT NULL COMMENT 'Der Betrag der Buchung',
  `ShopId` int(11) unsigned DEFAULT NULL,
  `Time` int(10) unsigned NOT NULL,
  `PayoutStatus` int(11) DEFAULT NULL,
  `PayoutMail` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=839 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_customers`
--

CREATE TABLE IF NOT EXISTS `mc_customers` (
`Id` int(11) unsigned NOT NULL,
  `Email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `FirstName` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `SurName` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `Token` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `EarningRate` int(11) NOT NULL DEFAULT '90',
  `PaypalMail` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MinecraftName` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LastShopId` int(11) DEFAULT NULL,
  `RegTime` int(11) DEFAULT NULL,
  `Validated` tinyint(1) DEFAULT NULL,
  `IsLoggedIn` tinyint(1) NOT NULL,
  `ResetPasswordToken` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=456 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_ench`
--

CREATE TABLE IF NOT EXISTS `mc_ench` (
`Id` int(10) unsigned NOT NULL,
  `ShopId` int(10) unsigned NOT NULL,
  `Name` varchar(45) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Die möglichen Verzauberungen' AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_EnchInItem`
--

CREATE TABLE IF NOT EXISTS `mc_EnchInItem` (
  `ShopId` int(11) NOT NULL,
  `ItemId` int(11) NOT NULL,
  `EnchId` int(11) NOT NULL,
  `Strength` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_failCountDelay`
--

CREATE TABLE IF NOT EXISTS `mc_failCountDelay` (
  `count` int(10) unsigned NOT NULL,
  `waitTime` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_gamer`
--

CREATE TABLE IF NOT EXISTS `mc_gamer` (
`Id` int(11) unsigned NOT NULL,
  `Nickname` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Password` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MinecraftName` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `RegTime` int(11) unsigned DEFAULT NULL,
  `Token` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `IsLoggedIn` tinyint(1) NOT NULL DEFAULT '0',
  `NewEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ResetPasswordToken` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LastLang` int(10) unsigned NOT NULL DEFAULT '0',
  `Validated` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, ob der Spieler validiert wurde (0: nicht validiert, >0: Zeitpunkt der validiernug)'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=231 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_gameraccounts`
--

CREATE TABLE IF NOT EXISTS `mc_gameraccounts` (
`Id` int(11) unsigned NOT NULL,
  `GamerId` int(11) unsigned NOT NULL,
  `Current` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Der aktuelle Kontostand in Punkten',
  `Difference` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Die Punkte der Buchung, die durch Geld gekauft wurden',
  `BonusDifference` int(11) NOT NULL DEFAULT '0' COMMENT 'Die Punkte der Buchung, die dem Spieler vom Shopbetreiber gutgeschrieben wurden bzw. abgezogen',
  `Action` enum('DEFAULT','INPAYMENT','BOUGHT_ITEM','BOUGHT_ITEM_BONUS','RECEIVED_BONUS','REMOVE_BONUS','RECEIVED_BONUS_FROM_INGAME') COLLATE utf8_unicode_ci DEFAULT NULL,
  `Time` int(10) unsigned NOT NULL,
  `InventoryId` int(11) unsigned DEFAULT NULL,
  `ShopId` int(11) unsigned DEFAULT NULL,
  `Revenue` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Der Cent-Wert von Difference'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=857 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_inventory`
--

CREATE TABLE IF NOT EXISTS `mc_inventory` (
`Id` int(11) NOT NULL,
  `ShopId` int(11) NOT NULL,
  `GamerId` int(11) NOT NULL,
  `ProductId` int(11) NOT NULL COMMENT 'Die Id des Produkts, das übertragen wurde',
  `TransferTime` int(11) DEFAULT NULL COMMENT 'Der Zeitpunkt, zu dem das Produkt übertragen wurde. Null, wenn das Produkt noch nicht übertragen werden konnte.',
  `Amount` int(11) NOT NULL,
  `DisabledUntil` int(11) DEFAULT NULL,
  `Cooldown` int(11) DEFAULT NULL,
  `CooldownInterval` enum('i','h','d','w','m') COLLATE utf8_unicode_ci DEFAULT NULL,
  `Locked` bigint(20) unsigned NOT NULL DEFAULT '0',
  `result` mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=415 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_items`
--

CREATE TABLE IF NOT EXISTS `mc_items` (
`Id` int(10) unsigned NOT NULL,
  `ShopId` int(10) unsigned NOT NULL,
  `Name` text CHARACTER SET utf8 NOT NULL COMMENT 'Die Bezeichnung des Items',
  `Ingame` text CHARACTER SET utf8 NOT NULL COMMENT 'Der Ingame-Name des Items',
  `MineId` int(11) unsigned NOT NULL COMMENT 'Die Minecraft-Id des Items',
  `Damage` int(11) DEFAULT NULL COMMENT 'sDamge-Wert',
  `Lore` text CHARACTER SET utf8 NOT NULL COMMENT 'Der Lore-Text',
  `Image` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Die Items, die in Produkten verwendet werden können' AUTO_INCREMENT=412 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_ItemsInProduct`
--

CREATE TABLE IF NOT EXISTS `mc_ItemsInProduct` (
  `ItemId` int(11) NOT NULL,
  `ProductId` int(11) NOT NULL,
  `ShopId` int(11) NOT NULL,
  `Amount` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_languages`
--

CREATE TABLE IF NOT EXISTS `mc_languages` (
`Id` int(11) unsigned NOT NULL,
  `Language` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Image` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Tag` char(5) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_loginerrors`
--

CREATE TABLE IF NOT EXISTS `mc_loginerrors` (
  `IP` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `AdminUser` tinyint(1) NOT NULL,
  `Count` int(11) NOT NULL DEFAULT '1',
  `Time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_messages`
--

CREATE TABLE IF NOT EXISTS `mc_messages` (
`Id` int(10) unsigned NOT NULL,
  `mail` varchar(255) CHARACTER SET utf8 NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8 NOT NULL,
  `message` mediumtext CHARACTER SET utf8 NOT NULL,
  `from` varchar(128) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_moneypackages`
--

CREATE TABLE IF NOT EXISTS `mc_moneypackages` (
  `Money` int(11) unsigned NOT NULL COMMENT 'Der Betrag in ganzen Euro',
  `Name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name des Pakets',
  `Checked` tinyint(1) NOT NULL DEFAULT '0',
  `Img` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Das anzuzeigende Bild'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_orders`
--

CREATE TABLE IF NOT EXISTS `mc_orders` (
`OrderId` int(11) unsigned NOT NULL,
  `TxnId` varchar(17) COLLATE utf8_unicode_ci NOT NULL,
  `UserId` int(11) unsigned NOT NULL,
  `PaypalTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_ouraccount`
--

CREATE TABLE IF NOT EXISTS `mc_ouraccount` (
`Id` int(11) unsigned NOT NULL,
  `Current` double DEFAULT NULL,
  `Difference` double DEFAULT NULL,
  `Time` int(10) unsigned NOT NULL,
  `PayoutMail` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_permittedshops`
--

CREATE TABLE IF NOT EXISTS `mc_permittedshops` (
  `GamerId` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned NOT NULL,
  `RegTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `BonusPoints` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt die Bonuspunkte an, die ein Use rin einem Shop hat',
  `PlayerOnline` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, ob der Spieler auf dem Server des Shops online ist. (Depricated?)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_productGroups`
--

CREATE TABLE IF NOT EXISTS `mc_productGroups` (
`Id` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned NOT NULL,
  `Label` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lft` int(11) unsigned NOT NULL DEFAULT '0',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0',
  `Description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Enabled` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=97 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_products`
--

CREATE TABLE IF NOT EXISTS `mc_products` (
`Id` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned NOT NULL,
  `Label` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Description` text COLLATE utf8_unicode_ci,
  `Image` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Points` int(11) NOT NULL DEFAULT '0',
  `Revenue` int(11) NOT NULL DEFAULT '0' COMMENT 'Generierter Umsatz in Euro-Cent',
  `GroupId` int(11) unsigned NOT NULL,
  `Enabled` tinyint(1) NOT NULL DEFAULT '1',
  `Cooldown` int(11) DEFAULT NULL,
  `CooldownInterval` enum('i','h','d','w','m') COLLATE utf8_unicode_ci DEFAULT NULL,
  `DisableDuringCooldown` tinyint(1) DEFAULT NULL COMMENT 'Gibt an, dass das Produkt während des Cooldowns nicht übertragen werden darf.',
  `NeedsPlayerOnline` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, dass der Spieler zur Übertragung der normalen Kommandos online sein muss.',
  `CooldownNeedsPlayer` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, dass der Spieler zur Übertragung der End-Kommandos online sein muss.',
  `BuyCounter` int(11) unsigned NOT NULL DEFAULT '0',
  `HasSetItems` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, wie viele Produkte verknüpft sind',
  `CustomImage` tinyint(1) DEFAULT NULL,
  `IsCustom` tinyint(1) NOT NULL DEFAULT '1',
  `CustomCommand` mediumtext COLLATE utf8_unicode_ci,
  `CustomCommandEnd` mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=630 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_ProductsInProduct`
--

CREATE TABLE IF NOT EXISTS `mc_ProductsInProduct` (
  `ParentProductId` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned NOT NULL,
  `ProductId` int(11) unsigned NOT NULL,
  `Amount` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Die Produkte, die in einem anderen Produkt enthalten sind';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_shops`
--

CREATE TABLE IF NOT EXISTS `mc_shops` (
`Id` int(11) unsigned NOT NULL,
  `Label` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Subdomain` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Domain` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `TemplateId` int(11) DEFAULT NULL,
  `CustomersId` int(11) DEFAULT NULL,
  `ShopNumber` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ServerHost` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ServerPort` int(5) NOT NULL,
  `ServerUser` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ServerPassword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ServerSalt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `RegTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ShopLogo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ShopLogoWidth` int(11) NOT NULL,
  `ShopLogoHeight` int(11) NOT NULL,
  `AverageProfit` double DEFAULT NULL,
  `AverageProfitCalcTime` timestamp NULL DEFAULT NULL,
  `CronLock` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, ob dieser Shop zur Zeit von einem Cronjob bearbeitet wird.',
  `ServerOnline` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, dass der Server zur Zeit online sein müsste. Ist dieser Wert auf 0, ist er definitiv offline.',
  `StartingCredit` int(10) unsigned NOT NULL DEFAULT '0',
  `BuyAgreement` mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=118 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_templates`
--

CREATE TABLE IF NOT EXISTS `mc_templates` (
`Id` int(11) unsigned NOT NULL,
  `Label` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Public` tinyint(1) DEFAULT NULL,
  `Directory` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_topmenu`
--

CREATE TABLE IF NOT EXISTS `mc_topmenu` (
`Id` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned NOT NULL,
  `Name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Link` text COLLATE utf8_unicode_ci NOT NULL,
  `Position` int(11) NOT NULL,
  `Target` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '_top'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mc_translations`
--

CREATE TABLE IF NOT EXISTS `mc_translations` (
  `Label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `LanguagesId` int(11) unsigned NOT NULL,
  `Translation` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `isUsed` tinyint(1) NOT NULL DEFAULT '0',
  `parseBBCode` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `z_mc_inventory`
--

CREATE TABLE IF NOT EXISTS `z_mc_inventory` (
`Id` int(11) unsigned NOT NULL,
  `ItemId` int(11) unsigned NOT NULL,
  `GamerId` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned NOT NULL,
  `Amount` int(11) unsigned NOT NULL,
  `Sum` int(11) NOT NULL,
  `isBuyAction` tinyint(1) DEFAULT '0',
  `Points` int(11) DEFAULT NULL,
  `isSet` tinyint(1) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=278 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `z_mc_transferCommands`
--

CREATE TABLE IF NOT EXISTS `z_mc_transferCommands` (
`Id` int(11) unsigned NOT NULL,
  `TransferId` int(11) unsigned NOT NULL COMMENT 'Die Id des übergeordnetesten Transfers, sodass erkannt werden kann, welche Kommandos auf einen Schlag übertragen werden sollten.\nWenn diese Spalte NULL ist bedeutet dies, dass dies die Parent-Zeile für die angegebene Übertragung ist. Falls das Produkt wäh',
  `ShopId` int(11) unsigned NOT NULL COMMENT 'Der Shop, für den das Produkt übertragen werden soll',
  `UserId` int(11) unsigned NOT NULL COMMENT 'Der User, für den das Produkt übertragen werden soll',
  `ProductId` int(11) unsigned NOT NULL COMMENT 'Die Id des Produkts, das übertragen werden soll',
  `Command` mediumtext COLLATE utf8_unicode_ci COMMENT 'Der Befehl im json-Format oder als Klartext.',
  `CallNumber` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Die Anzahl, wie oft das Produkt übertragen werden soll bzw. was an der Stelle %%AMOUNT%% eingesetzt werden muss.',
  `ExecutionTime` int(11) unsigned DEFAULT NULL COMMENT 'Der Zeitpunkt, zu dem der Transfer ausgeführt werden soll',
  `ScheduledExecutionTime` int(11) unsigned DEFAULT NULL COMMENT 'Der Zeitpunkt, zu dem der Transfer tatsächlich ausgeführt wurde',
  `TransferResult` mediumtext COLLATE utf8_unicode_ci COMMENT 'Das von der jsonApi zurückgelieferte Ergebnis des Commands',
  `IgnoreCommand` tinyint(1) NOT NULL COMMENT 'Gibt an, dass dieses Kommando ignoriert werden soll, da das Kommando ein EndCommand war und dasselbe Produkt erneut übertragen wurde.\nBei einem normal eingetragenen EndCommand steht in diesem Feld eine 2',
  `FailCount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, wie oft die Übertragung fehlgeschlagen ist (relevant für automatisch zu übertragenden Kommandos)',
  `Locked` bigint(20) unsigned NOT NULL,
  `NeedsPlayerOnline` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Cooldown` int(10) unsigned DEFAULT NULL,
  `CooldownInterval` enum('m','h','d','w','M') COLLATE utf8_unicode_ci DEFAULT NULL,
  `Type` enum('BY_USER','BY_CRON') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BY_USER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `z_mc_transfers`
--

CREATE TABLE IF NOT EXISTS `z_mc_transfers` (
`Id` int(11) unsigned NOT NULL,
  `ShopId` int(11) unsigned NOT NULL COMMENT 'Der Shop, für den das Produkt übertragen werden soll',
  `UserId` int(11) unsigned NOT NULL COMMENT 'Der User, für den das Produkt übertragen werden soll',
  `ProductId` int(11) unsigned NOT NULL COMMENT 'Die Id des Produkts, das übertragen werden soll',
  `CallNumber` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Die Anzahl, wie oft das Produkt übertragen werden soll bzw. was an der Stelle %%AMOUNT%% eingesetzt werden muss.',
  `ExecutionTime` int(11) unsigned DEFAULT NULL COMMENT 'Der Zeitpunkt, zu dem der Transfer (vom User!) ausgeführt wurde.',
  `TransferResult` mediumtext COLLATE utf8_unicode_ci COMMENT 'Das von der jsonApi zurückgelieferte Ergebnis des Commands',
  `FailCount` int(10) unsigned DEFAULT '0' COMMENT 'Die Anzahl der fehlgeschlagenen Übertragungen. Auch bei einer fehlgeschlagenen Übertragung wird ExecutionTime gesetzt.',
  `Locked` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, ob dieses Produkt gerade behandelt wird und von einem weiteren cronjob jetzt nicht berücksichtigt werden darf. Wird nur gesetzt, wenn dieses Produkt Kommandos enthält, die per Cronjob ausgeführt werden müssen.',
  `Cooldown` int(11) DEFAULT NULL,
  `CooldownInterval` enum('m','h','d','w','M') COLLATE utf8_unicode_ci DEFAULT NULL,
  `DisabledUntil` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Gibt an, bis zu welchem Zeitpunkt ein gleichartiges Produkt nicht übertragen werden darf.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `betamails`
--
ALTER TABLE `betamails`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `value_UNIQUE` (`mail`);

--
-- Indexes for table `mc_boxlayout`
--
ALTER TABLE `mc_boxlayout`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_customcss`
--
ALTER TABLE `mc_customcss`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_customeraccounts`
--
ALTER TABLE `mc_customeraccounts`
 ADD PRIMARY KEY (`Id`), ADD KEY `Time` (`Time`);

--
-- Indexes for table `mc_customers`
--
ALTER TABLE `mc_customers`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_ench`
--
ALTER TABLE `mc_ench`
 ADD PRIMARY KEY (`Id`,`ShopId`), ADD UNIQUE KEY `Id_UNIQUE` (`Id`);

--
-- Indexes for table `mc_EnchInItem`
--
ALTER TABLE `mc_EnchInItem`
 ADD PRIMARY KEY (`ShopId`,`ItemId`,`EnchId`);

--
-- Indexes for table `mc_failCountDelay`
--
ALTER TABLE `mc_failCountDelay`
 ADD PRIMARY KEY (`count`,`waitTime`);

--
-- Indexes for table `mc_gamer`
--
ALTER TABLE `mc_gamer`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_gameraccounts`
--
ALTER TABLE `mc_gameraccounts`
 ADD PRIMARY KEY (`Id`), ADD KEY `PlayerFromShop` (`GamerId`,`ShopId`);

--
-- Indexes for table `mc_inventory`
--
ALTER TABLE `mc_inventory`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_items`
--
ALTER TABLE `mc_items`
 ADD PRIMARY KEY (`Id`,`ShopId`);

--
-- Indexes for table `mc_ItemsInProduct`
--
ALTER TABLE `mc_ItemsInProduct`
 ADD PRIMARY KEY (`ItemId`,`ProductId`,`ShopId`);

--
-- Indexes for table `mc_languages`
--
ALTER TABLE `mc_languages`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_loginerrors`
--
ALTER TABLE `mc_loginerrors`
 ADD PRIMARY KEY (`IP`,`AdminUser`);

--
-- Indexes for table `mc_messages`
--
ALTER TABLE `mc_messages`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_moneypackages`
--
ALTER TABLE `mc_moneypackages`
 ADD PRIMARY KEY (`Money`), ADD UNIQUE KEY `Img_UNIQUE` (`Img`);

--
-- Indexes for table `mc_orders`
--
ALTER TABLE `mc_orders`
 ADD PRIMARY KEY (`OrderId`), ADD UNIQUE KEY `txn_id` (`TxnId`);

--
-- Indexes for table `mc_ouraccount`
--
ALTER TABLE `mc_ouraccount`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_permittedshops`
--
ALTER TABLE `mc_permittedshops`
 ADD PRIMARY KEY (`GamerId`,`ShopId`);

--
-- Indexes for table `mc_productGroups`
--
ALTER TABLE `mc_productGroups`
 ADD PRIMARY KEY (`Id`,`ShopId`), ADD KEY `rgt` (`rgt`), ADD KEY `lft` (`ShopId`,`lft`);

--
-- Indexes for table `mc_products`
--
ALTER TABLE `mc_products`
 ADD PRIMARY KEY (`Id`,`ShopId`);

--
-- Indexes for table `mc_ProductsInProduct`
--
ALTER TABLE `mc_ProductsInProduct`
 ADD PRIMARY KEY (`ParentProductId`,`ShopId`,`ProductId`);

--
-- Indexes for table `mc_shops`
--
ALTER TABLE `mc_shops`
 ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `Subdomain_UNIQUE` (`Subdomain`);

--
-- Indexes for table `mc_templates`
--
ALTER TABLE `mc_templates`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mc_topmenu`
--
ALTER TABLE `mc_topmenu`
 ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `Id_UNIQUE` (`Id`);

--
-- Indexes for table `mc_translations`
--
ALTER TABLE `mc_translations`
 ADD PRIMARY KEY (`Label`,`LanguagesId`), ADD KEY `Lang` (`LanguagesId`);

--
-- Indexes for table `z_mc_inventory`
--
ALTER TABLE `z_mc_inventory`
 ADD PRIMARY KEY (`Id`), ADD KEY `Schedule` (`ItemId`,`GamerId`,`ShopId`,`time`);

--
-- Indexes for table `z_mc_transferCommands`
--
ALTER TABLE `z_mc_transferCommands`
 ADD PRIMARY KEY (`Id`), ADD KEY `SetLock` (`ShopId`,`ExecutionTime`,`IgnoreCommand`,`ScheduledExecutionTime`), ADD KEY `Locked` (`Locked`);

--
-- Indexes for table `z_mc_transfers`
--
ALTER TABLE `z_mc_transfers`
 ADD PRIMARY KEY (`Id`), ADD KEY `Locked` (`Locked`), ADD KEY `SetLock` (`Locked`,`ShopId`,`ExecutionTime`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `betamails`
--
ALTER TABLE `betamails`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1766;
--
-- AUTO_INCREMENT for table `mc_boxlayout`
--
ALTER TABLE `mc_boxlayout`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mc_customcss`
--
ALTER TABLE `mc_customcss`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `mc_customeraccounts`
--
ALTER TABLE `mc_customeraccounts`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=839;
--
-- AUTO_INCREMENT for table `mc_customers`
--
ALTER TABLE `mc_customers`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=456;
--
-- AUTO_INCREMENT for table `mc_ench`
--
ALTER TABLE `mc_ench`
MODIFY `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `mc_gamer`
--
ALTER TABLE `mc_gamer`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=231;
--
-- AUTO_INCREMENT for table `mc_gameraccounts`
--
ALTER TABLE `mc_gameraccounts`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=857;
--
-- AUTO_INCREMENT for table `mc_inventory`
--
ALTER TABLE `mc_inventory`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=415;
--
-- AUTO_INCREMENT for table `mc_items`
--
ALTER TABLE `mc_items`
MODIFY `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=412;
--
-- AUTO_INCREMENT for table `mc_languages`
--
ALTER TABLE `mc_languages`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `mc_messages`
--
ALTER TABLE `mc_messages`
MODIFY `Id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mc_orders`
--
ALTER TABLE `mc_orders`
MODIFY `OrderId` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mc_ouraccount`
--
ALTER TABLE `mc_ouraccount`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mc_productGroups`
--
ALTER TABLE `mc_productGroups`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT for table `mc_products`
--
ALTER TABLE `mc_products`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=630;
--
-- AUTO_INCREMENT for table `mc_shops`
--
ALTER TABLE `mc_shops`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `mc_templates`
--
ALTER TABLE `mc_templates`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `mc_topmenu`
--
ALTER TABLE `mc_topmenu`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `z_mc_inventory`
--
ALTER TABLE `z_mc_inventory`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=278;
--
-- AUTO_INCREMENT for table `z_mc_transferCommands`
--
ALTER TABLE `z_mc_transferCommands`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `z_mc_transfers`
--
ALTER TABLE `z_mc_transfers`
MODIFY `Id` int(11) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
