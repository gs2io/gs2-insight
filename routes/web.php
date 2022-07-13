<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','App\Http\Controllers\HomeController@index');

Route::get('/players','App\Http\Controllers\PlayersController@list');
Route::get('/players/{userId}','App\Http\Controllers\PlayersController@info');
Route::get('/players/{userId}/reload','App\Http\Controllers\PlayersController@reload');
Route::get('/players/{userId}/timelines/{transactionId}','App\Http\Controllers\TimelineController@info');

Route::get('/players/{userId}/account/{namespaceName}','App\Http\Controllers\AccountController@namespace');
Route::get('/players/{userId}/account/{namespaceName}/takeOver/{type}','App\Http\Controllers\AccountController@takeOver');
Route::post('/players/{userId}/account/{namespaceName}/takeOver/add','App\Http\Controllers\AccountController@add');
Route::post('/players/{userId}/account/{namespaceName}/takeOver/{type}/{userIdentifier}/delete','App\Http\Controllers\AccountController@delete');

Route::get('/players/{userId}/quest/{namespaceName}','App\Http\Controllers\QuestController@namespace');
Route::get('/players/{userId}/quest/{namespaceName}/questGroup/{questGroupModelName}','App\Http\Controllers\QuestController@questGroup');
Route::get('/players/{userId}/quest/{namespaceName}/questGroup/{questGroupModelName}/quest/{questModelName}','App\Http\Controllers\QuestController@quest');
Route::post('/players/{userId}/quest/{namespaceName}/progress/{questGroupModelName}/start','App\Http\Controllers\QuestController@start');
Route::post('/players/{userId}/quest/{namespaceName}/progress/complete','App\Http\Controllers\QuestController@complete');
Route::post('/players/{userId}/quest/{namespaceName}/progress/failed','App\Http\Controllers\QuestController@failed');
Route::post('/players/{userId}/quest/{namespaceName}/progress/delete','App\Http\Controllers\QuestController@delete');
Route::post('/players/{userId}/quest/{namespaceName}/questGroup/{questGroupModelName}/complete/reset','App\Http\Controllers\QuestController@resetCompleted');

Route::get('/players/{userId}/inventory/{namespaceName}','App\Http\Controllers\InventoryController@namespace');
Route::get('/players/{userId}/inventory/{namespaceName}/inventory/{inventoryModelName}','App\Http\Controllers\InventoryController@inventory');
Route::get('/players/{userId}/inventory/{namespaceName}/inventory/{inventoryModelName}/item/{itemModelName}','App\Http\Controllers\InventoryController@item');
Route::post('/players/{userId}/inventory/{namespaceName}/inventory/{inventoryModelName}/acquire','App\Http\Controllers\InventoryController@acquire');
Route::post('/players/{userId}/inventory/{namespaceName}/inventory/{inventoryModelName}/consume','App\Http\Controllers\InventoryController@consume');
Route::post('/players/{userId}/inventory/{namespaceName}/inventory/{inventoryModelName}/capacity/update','App\Http\Controllers\InventoryController@updateCapacity');

Route::get('/players/{userId}/experience/{namespaceName}','App\Http\Controllers\ExperienceController@namespace');
Route::get('/players/{userId}/experience/{namespaceName}/experience/{experienceModelName}','App\Http\Controllers\ExperienceController@experience');
Route::get('/players/{userId}/experience/{namespaceName}/experience/{experienceModelName}/status/{propertyId}','App\Http\Controllers\ExperienceController@status');
Route::post('/players/{userId}/experience/{namespaceName}/experience/{experienceModelName}/status/experience/add','App\Http\Controllers\ExperienceController@addExperience');
Route::post('/players/{userId}/experience/{namespaceName}/experience/{experienceModelName}/status/experience/set','App\Http\Controllers\ExperienceController@setExperience');
Route::post('/players/{userId}/experience/{namespaceName}/experience/{experienceModelName}/status/rankCap/add','App\Http\Controllers\ExperienceController@addRankCap');
Route::post('/players/{userId}/experience/{namespaceName}/experience/{experienceModelName}/status/rankCap/set','App\Http\Controllers\ExperienceController@setRankCap');
Route::post('/players/{userId}/experience/{namespaceName}/experience/{experienceModelName}/status/reset','App\Http\Controllers\ExperienceController@reset');

Route::get('/players/{userId}/money/{namespaceName}','App\Http\Controllers\MoneyController@namespace');
Route::get('/players/{userId}/money/{namespaceName}/wallet/{slot}','App\Http\Controllers\MoneyController@wallet');
Route::post('/players/{userId}/money/{namespaceName}/{slot}/withdraw','App\Http\Controllers\MoneyController@withdraw');
Route::post('/players/{userId}/money/{namespaceName}/{slot}/deposit','App\Http\Controllers\MoneyController@deposit');

Route::get('/players/{userId}/mission/{namespaceName}','App\Http\Controllers\MissionController@namespace');
Route::get('/players/{userId}/mission/{namespaceName}/missionGroup/{missionGroupModelName}','App\Http\Controllers\MissionController@missionGroup');
Route::get('/players/{userId}/mission/{namespaceName}/missionGroup/{missionGroupModelName}/missionTask/{missionTaskModelName}','App\Http\Controllers\MissionController@missionTask');
Route::get('/players/{userId}/mission/{namespaceName}/counter/{counterModelName}','App\Http\Controllers\MissionController@counter');
Route::post('/players/{userId}/mission/{namespaceName}/missionGroup/{missionGroupModelName}/missionTask/receive','App\Http\Controllers\MissionController@receive');
Route::post('/players/{userId}/mission/{namespaceName}/missionGroup/{missionGroupModelName}/complete/reset','App\Http\Controllers\MissionController@resetComplete');
Route::post('/players/{userId}/mission/{namespaceName}/counter/increase','App\Http\Controllers\MissionController@increase');
Route::post('/players/{userId}/mission/{namespaceName}/counter/{counterModelName}/reset','App\Http\Controllers\MissionController@resetCounter');

Route::get('/players/{userId}/stamina/{namespaceName}','App\Http\Controllers\StaminaController@namespace');
Route::get('/players/{userId}/stamina/{namespaceName}/stamina/{staminaModelName}','App\Http\Controllers\StaminaController@stamina');
Route::post('/players/{userId}/stamina/{namespaceName}/stamina/consume','App\Http\Controllers\StaminaController@consume');
Route::post('/players/{userId}/stamina/{namespaceName}/stamina/recover','App\Http\Controllers\StaminaController@recover');

Route::get('/players/{userId}/dictionary/{namespaceName}','App\Http\Controllers\DictionaryController@namespace');
Route::get('/players/{userId}/dictionary/{namespaceName}/entry/{entryModelName}','App\Http\Controllers\DictionaryController@entry');
Route::post('/players/{userId}/dictionary/{namespaceName}/entry/add','App\Http\Controllers\DictionaryController@add');
Route::post('/players/{userId}/dictionary/{namespaceName}/entry/reset','App\Http\Controllers\DictionaryController@reset');

Route::get('/players/{userId}/inbox/{namespaceName}','App\Http\Controllers\InboxController@namespace');
Route::get('/players/{userId}/inbox/{namespaceName}/message/{messageName}','App\Http\Controllers\InboxController@message');
Route::post('/players/{userId}/inbox/{namespaceName}/message/read','App\Http\Controllers\InboxController@read');
Route::post('/players/{userId}/inbox/{namespaceName}/message/delete','App\Http\Controllers\InboxController@delete');

Route::get('/players/{userId}/datastore/{namespaceName}','App\Http\Controllers\DatastoreController@namespace');
Route::get('/players/{userId}/datastore/{namespaceName}/dataObject/{dataObjectName}','App\Http\Controllers\DatastoreController@dataObject');
Route::post('/players/{userId}/datastore/{namespaceName}/dataObject/download','App\Http\Controllers\DatastoreController@download');
Route::post('/players/{userId}/datastore/{namespaceName}/dataObject/delete','App\Http\Controllers\DatastoreController@delete');

Route::get('/players/{userId}/friend/{namespaceName}','App\Http\Controllers\FriendController@namespace');
Route::get('/players/{userId}/friend/{namespaceName}/friend/{targetUserId}','App\Http\Controllers\FriendController@friend');
Route::get('/players/{userId}/friend/{namespaceName}/follower/{targetUserId}','App\Http\Controllers\FriendController@follower');
Route::post('/players/{userId}/friend/{namespaceName}/friend/delete','App\Http\Controllers\FriendController@deleteFriend');
Route::post('/players/{userId}/friend/{namespaceName}/follower/follow','App\Http\Controllers\FriendController@follow');
Route::post('/players/{userId}/friend/{namespaceName}/follower/unfollow','App\Http\Controllers\FriendController@unfollow');
Route::post('/players/{userId}/friend/{namespaceName}/sendRequest/send','App\Http\Controllers\FriendController@sendRequest');
Route::post('/players/{userId}/friend/{namespaceName}/sendRequest/delete','App\Http\Controllers\FriendController@deleteRequest');
Route::post('/players/{userId}/friend/{namespaceName}/receiveRequest/accept','App\Http\Controllers\FriendController@acceptRequest');
Route::post('/players/{userId}/friend/{namespaceName}/receiveRequest/reject','App\Http\Controllers\FriendController@rejectRequest');
Route::post('/players/{userId}/friend/{namespaceName}/profile/update','App\Http\Controllers\FriendController@updateProfile');

Route::get('/players/{userId}/chat/{namespaceName}','App\Http\Controllers\ChatController@namespace');
Route::get('/players/{userId}/chat/{namespaceName}/room/{roomName}','App\Http\Controllers\ChatController@room');
Route::post('/players/{userId}/chat/{namespaceName}/subscribe/add','App\Http\Controllers\ChatController@add');
Route::post('/players/{userId}/chat/{namespaceName}/subscribe/delete','App\Http\Controllers\ChatController@delete');
Route::post('/players/{userId}/chat/{namespaceName}/subscribe/{roomName}/delete','App\Http\Controllers\ChatController@delete');

Route::get('/players/{userId}/exchange/{namespaceName}','App\Http\Controllers\ExchangeController@namespace');
Route::get('/players/{userId}/exchange/{namespaceName}/rate/{rateModelName}','App\Http\Controllers\ExchangeController@rate');
Route::post('/players/{userId}/exchange/{namespaceName}/rate/exchange','App\Http\Controllers\ExchangeController@exchange');

Route::get('/players/{userId}/showcase/{namespaceName}','App\Http\Controllers\ShowcaseController@namespace');
Route::get('/players/{userId}/showcase/{namespaceName}/showcase/{showcaseModelName}','App\Http\Controllers\ShowcaseController@showcase');
Route::get('/players/{userId}/showcase/{namespaceName}/showcase/{showcaseModelName}/displayItem/{displayItemId}','App\Http\Controllers\ShowcaseController@displayItem');
Route::post('/players/{userId}/showcase/{namespaceName}/showcase/{showcaseModelName}/displayItem/buy','App\Http\Controllers\ShowcaseController@buy');

Route::get('/players/{userId}/ranking/{namespaceName}','App\Http\Controllers\RankingController@namespace');
Route::get('/players/{userId}/ranking/{namespaceName}/category/{categoryModelName}','App\Http\Controllers\RankingController@category');

Route::get('/players/{userId}/jobQueue/{namespaceName}','App\Http\Controllers\JobQueueController@namespace');
Route::get('/players/{userId}/jobQueue/{namespaceName}/job/{jobName}','App\Http\Controllers\JobQueueController@job');
Route::post('/players/{userId}/jobQueue/{namespaceName}/job/run','App\Http\Controllers\JobQueueController@run');

Route::get('/players/{userId}/limit/{namespaceName}','App\Http\Controllers\LimitController@namespace');
Route::get('/players/{userId}/limit/{namespaceName}/limit/{limitModelName}','App\Http\Controllers\LimitController@limit');
Route::get('/players/{userId}/limit/{namespaceName}/limit/{limitModelName}/counter/{counterName}','App\Http\Controllers\LimitController@counter');
Route::post('/players/{userId}/limit/{namespaceName}/limit/counter/increase','App\Http\Controllers\LimitController@increase');
Route::post('/players/{userId}/limit/{namespaceName}/limit/counter/reset','App\Http\Controllers\LimitController@reset');

Route::get('/players/{userId}/lottery/{namespaceName}','App\Http\Controllers\LotteryController@namespace');
Route::get('/players/{userId}/lottery/{namespaceName}/lottery/{lotteryModelName}','App\Http\Controllers\LotteryController@lottery');
Route::post('/players/{userId}/lottery/{namespaceName}/lottery/lottery','App\Http\Controllers\LotteryController@draw');

Route::post('/players/{userId}/friend/{namespaceName}/follow/follow','App\Http\Controllers\FriendController@follow');
Route::post('/players/{userId}/friend/{namespaceName}/follow/{targetUserId}/unfollow','App\Http\Controllers\FriendController@unfollow');
Route::post('/players/{userId}/friend/{namespaceName}/friend/{targetUserId}/delete','App\Http\Controllers\FriendController@deleteFriend');
Route::post('/players/{userId}/friend/{namespaceName}/friend/request/send','App\Http\Controllers\FriendController@send');
Route::post('/players/{userId}/friend/{namespaceName}/friend/request/{targetUserId}/accept','App\Http\Controllers\FriendController@accept');
Route::post('/players/{userId}/friend/{namespaceName}/friend/request/{targetUserId}/reject','App\Http\Controllers\FriendController@reject');
Route::post('/players/{userId}/friend/{namespaceName}/friend/request/{targetUserId}/delete','App\Http\Controllers\FriendController@deleteRequest');


Route::get('/metrics','App\Http\Controllers\MetricsController@index');
Route::get('/metrics/account/{namespaceName}','App\Http\Controllers\Metrics\Account\NamespaceController@index');
Route::get('/metrics/chat/{namespaceName}','App\Http\Controllers\Metrics\Chat\NamespaceController@index');
Route::get('/metrics/chat/{namespaceName}/room/{roomName}','App\Http\Controllers\Metrics\Chat\RoomController@index');
Route::get('/metrics/datastore/{namespaceName}','App\Http\Controllers\Metrics\Datastore\NamespaceController@index');
Route::get('/metrics/dictionary/{namespaceName}','App\Http\Controllers\Metrics\Dictionary\NamespaceController@index');
Route::get('/metrics/dictionary/{namespaceName}/entryModel/{entryModelName}','App\Http\Controllers\Metrics\Dictionary\EntryModelController@index');
Route::get('/metrics/exchange/{namespaceName}','App\Http\Controllers\Metrics\Exchange\NamespaceController@index');
Route::get('/metrics/exchange/{namespaceName}/rateModel/{rateModelName}','App\Http\Controllers\Metrics\Exchange\RateModelController@index');
Route::get('/metrics/experience/{namespaceName}','App\Http\Controllers\Metrics\Experience\NamespaceController@index');
Route::get('/metrics/experience/{namespaceName}/experienceModel/{experienceModelName}','App\Http\Controllers\Metrics\Experience\ExperienceModelController@index');
Route::get('/metrics/friend/{namespaceName}','App\Http\Controllers\Metrics\Friend\NamespaceController@index');
Route::get('/metrics/inbox/{namespaceName}','App\Http\Controllers\Metrics\Inbox\NamespaceController@index');
Route::get('/metrics/inventory/{namespaceName}','App\Http\Controllers\Metrics\Inventory\NamespaceController@index');
Route::get('/metrics/inventory/{namespaceName}/inventoryModel/{inventoryModelName}','App\Http\Controllers\Metrics\Inventory\InventoryModelController@index');
Route::get('/metrics/inventory/{namespaceName}/inventoryModel/{inventoryModelName}/itemModel/{itemModelName}','App\Http\Controllers\Metrics\Inventory\ItemModelController@index');
Route::get('/metrics/jobQueue/{namespaceName}','App\Http\Controllers\Metrics\JobQueue\NamespaceController@index');
Route::get('/metrics/limit/{namespaceName}','App\Http\Controllers\Metrics\Limit\NamespaceController@index');
Route::get('/metrics/limit/{namespaceName}/limitModel/{limitModelName}','App\Http\Controllers\Metrics\Limit\LimitModelController@index');
Route::get('/metrics/lottery/{namespaceName}','App\Http\Controllers\Metrics\Lottery\NamespaceController@index');
Route::get('/metrics/lottery/{namespaceName}/lotteryModel/{lotteryModelName}','App\Http\Controllers\Metrics\Lottery\LotteryModelController@index');
Route::get('/metrics/matchmaking/{namespaceName}','App\Http\Controllers\Metrics\Matchmaking\NamespaceController@index');
Route::get('/metrics/mission/{namespaceName}','App\Http\Controllers\Metrics\Mission\NamespaceController@index');
Route::get('/metrics/mission/{namespaceName}/counterModel/{counterModelName}','App\Http\Controllers\Metrics\Mission\CounterModelController@index');
Route::get('/metrics/mission/{namespaceName}/missionGroupModel/{missionGroupModelName}','App\Http\Controllers\Metrics\Mission\MissionGroupModelController@index');
Route::get('/metrics/mission/{namespaceName}/missionGroupModel/{missionGroupModelName}/missionTaskModel/{missionTaskModelName}','App\Http\Controllers\Metrics\Mission\MissionTaskModelController@index');
Route::get('/metrics/money/{namespaceName}','App\Http\Controllers\Metrics\Money\NamespaceController@index');
Route::get('/metrics/money/{namespaceName}/content/{contentsId}','App\Http\Controllers\Metrics\Money\ContentController@index');
Route::get('/metrics/quest/{namespaceName}','App\Http\Controllers\Metrics\Quest\NamespaceController@index');
Route::get('/metrics/quest/{namespaceName}/questGroupModel/{questGroupModelName}','App\Http\Controllers\Metrics\Quest\QuestGroupModelController@index');
Route::get('/metrics/quest/{namespaceName}/questGroupModel/{questGroupModelName}/questModel/{questModelName}','App\Http\Controllers\Metrics\Quest\QuestModelController@index');
Route::get('/metrics/ranking/{namespaceName}','App\Http\Controllers\Metrics\Ranking\NamespaceController@index');
Route::get('/metrics/ranking/{namespaceName}/categoryModel/{categoryModelName}','App\Http\Controllers\Metrics\Ranking\CategoryModelController@index');
Route::get('/metrics/realtime/{namespaceName}','App\Http\Controllers\Metrics\Realtime\NamespaceController@index');
Route::get('/metrics/schedule/{namespaceName}','App\Http\Controllers\Metrics\Schedule\NamespaceController@index');
Route::get('/metrics/schedule/{namespaceName}/eventModel/{eventModelName}','App\Http\Controllers\Metrics\Schedule\EventModelController@index');
Route::get('/metrics/schedule/{namespaceName}/triggerModel/{triggerModelName}','App\Http\Controllers\Metrics\Schedule\TriggerModelController@index');
Route::get('/metrics/script/{namespaceName}','App\Http\Controllers\Metrics\Script\NamespaceController@index');
Route::get('/metrics/script/{namespaceName}/scriptModel/{scriptModelName}','App\Http\Controllers\Metrics\Script\ScriptModelController@index');
Route::get('/metrics/showcase/{namespaceName}','App\Http\Controllers\Metrics\Showcase\NamespaceController@index');
Route::get('/metrics/showcase/{namespaceName}/showcaseModel/{showcaseModelName}','App\Http\Controllers\Metrics\Showcase\ShowcaseModelController@index');
Route::get('/metrics/showcase/{namespaceName}/showcaseModel/{showcaseModelName}/displayItemModel/{displayItemId}','App\Http\Controllers\Metrics\Showcase\DisplayItemModelController@index');
Route::get('/metrics/stamina/{namespaceName}','App\Http\Controllers\Metrics\Stamina\NamespaceController@index');
Route::get('/metrics/stamina/{namespaceName}/staminaModel/{staminaModelName}','App\Http\Controllers\Metrics\Stamina\StaminaModelController@index');

Route::get('/gcp','App\Http\Controllers\GcpController@index');
Route::post('/gcp/create','App\Http\Controllers\GcpController@create');
Route::post('/gcp/update','App\Http\Controllers\GcpController@update');
Route::get('/gcp/load','App\Http\Controllers\GcpController@load');
Route::get('/gcp/load/{userId}','App\Http\Controllers\GcpController@loadDetail');

Route::get('/gs2','App\Http\Controllers\Gs2Controller@index');
Route::post('/gs2/create','App\Http\Controllers\Gs2Controller@create');
Route::post('/gs2/update','App\Http\Controllers\Gs2Controller@update');
