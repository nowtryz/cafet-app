<?php
namespace cafetapi\modules\rest\stats;

use cafetapi\io\StatsManager;
use cafetapi\io\UserManager;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\user\Perm;

/**
 *
 * @author damie
 *
 */
class StatsNode implements RestNode
{
    const OVERVIEW = 'overview';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();


        switch ($dir) {
            case self::OVERVIEW:   return self::overview($request);

            case null: return ClientError::forbidden();
            default:   return ClientError::resourceNotFound('Unknown stats/' . $dir . ' node');
        }
    }

    private static function overview(Rest $request) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_STATS);

        $used = $total = 0;
        storage_info($used, $total);

        $manager = StatsManager::getInstance();

        return new RestResponse(200, HttpCodes::HTTP_200, array(
            'total_storage' => $total,
            'used_storage' => $used,
            'weekly_revenue' => $manager->getWeeklyRevenue(),
            'monthly_sales' => $manager->getMonthlySales(),
            'user_count' => UserManager::getInstance()->count(),
            'weekly_balance_reloads' => $manager->getWeeklyBalanceReloads(),
            'last_monthly_sales_count' => $manager->getLastMonthlySalesCount(),
            'monthly_subscription' => $manager->getMonthlySubscription()
        ));
    }
}

