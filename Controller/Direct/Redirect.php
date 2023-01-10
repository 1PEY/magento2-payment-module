<?php
/*
 * Copyright (C) 2018 1PEY
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      1PEY
 * @copyright   2018 1PEY
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

namespace OnePEY\OnePEY\Controller\Direct;

/**
 * Return Action Controller (used to handle Redirects from the Payment Gateway)
 *
 * Class Redirect
 * @package OnePEY\OnePEY\Controller\Direct
 */
class Redirect extends \OnePEY\OnePEY\Controller\AbstractCheckoutRedirectAction
{
    /**
     * Handle the result from the Payment Gateway
     *
     * @return void
     */
    public function execute()
    {
        switch ($this->getReturnAction()) {
            case 'success':
                $this->executeSuccessAction();
                break;

            case 'cancel':
                $this->getMessageManager()->addWarning(
                    __("You have successfully canceled your order")
                );
                $this->executeCancelAction();
                break;

            case 'failure':
                /**
                 * If the customer is redirected here after processing Server to Server 3D-Secure transaction
                 * this mean the Payment Transaction Status has been set to "Pending Async".
                 * So there should be a problem with the 3-D Secure Code Authentication, but the
                 * exact error message from the payment gateway will be delivered after processing the
                 * notification from the gateway
                 */
                $this->getMessageManager()->addError(
                    __('Please, check if the used card supports 3-D Secure and you have entered ' .
                       'a valid 3-D Secure code! Please try again!')
                );
                $this->executeCancelAction();
                break;

            default:
                $this->getResponse()->setHttpResponseCode(
                    \Magento\Framework\Webapi\Exception::HTTP_UNAUTHORIZED
                );
        }
    }
}
