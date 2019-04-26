<?php
namespace cafetapi;

use cafetapi\data\Client;
use cafetapi\data\Expense;
use cafetapi\data\Reload;
use cafetapi\user\User;
use cafetapi\data\People;

class MailManager
{    
    public static function paymentNotice(Client $client, Expense $expense) {
        $mail = new Mail('payment_notice', $client->getEmail());
        $mail->setVar('surname', $client->getFirstname());
        $mail->setVar('name', $client->getFamilyName());
        $mail->setVar('balance', number_format($expense->getBalanceAfterTransaction(), 2, ',', ' '));
        $mail->setVar('total', number_format($expense->getTotal(), 2, ',', ' '));
        $mail->setVar('date', $expense->getDate()->toDateTime()->format('d/m/Y @ G:i'));
        
        $details = '';
        foreach ($expense->getDetails() as $detail) {
            $details .= '<tr><td>' . $detail->getName() . '</td><td>' . $detail->getQuantity() . '</td><td>' . number_format($detail->getPrice(), 2, ',', ' ') . ' €' . '</td><td>' . number_format($detail->getPrice() * $detail->getQuantity(), 2, ',', ' ') . ' €' . '</td></tr>';
        }
        $mail->setVar('expense', $details);
        
        return $mail;
    }
    
    public static function reloadRequest(Client $client) {
        $mail = new Mail('reload_request', $client->getEmail());
        $mail->setVar('surname', $client->getFirstname());
        $mail->setVar('name', $client->getFamilyName());
        $mail->setVar('balance', number_format($client->getBalance(), 2, ',', ' '));
        
        $expenses = '';
        foreach ($client->getLastExpenses() as $expense) {
            $expenses .= '<tr><td>' . 'Le ' . $expense->getDate()->getFormatedDate() . ' à ' . $expense->getDate()->getFormatedTime() . '</td><td>' . number_format($expense->getTotal(), 2, ',', ' ') . ' €' . '</td><td>' . number_format($expense->getBalanceAfterTransaction(), 2, ',', ' ') . ' €' . '</td></tr>';
        }
        $mail->setVar('expenses', $expenses);
        
        return $mail;
    }
    
    public static function welcome(User $user) {
        $mail = new Mail('welcome', $user->getEmail());
        $mail->setVar('surname', $user->getFirstname());
        $mail->setVar('name', $user->getFamilyName());
        return $mail;
    }
    
    public static function message(People $people, $message) {
        $mail = new Mail('message', $people->getEmail());
        $mail->setVar('surname', $people->getFirstname());
        $mail->setVar('name', $people->getFamilyName());
        $mail->setVar('message', $message);
        return $mail;
    }
    
    public static function reloadNotice(Client $client, Reload $reload) {
        $mail = new Mail('reload_notice', $client->getEmail());
        $mail->setVar('surname', $client->getFirstname());
        $mail->setVar('name', $client->getFamilyName());
        $mail->setVar('balance', number_format($reload->getBalanceAfterTransaction(), 2, ',', ' '));
        $mail->setVar('date', $reload->getDate()->toDateTime()->format('d/m/Y @ G:i'));
        $mail->setVar('amount', number_format($reload->getAmount(), 2, ',', ' '));
        $mail->setVar('transation_reference', dechex($reload->getId()));
        $mail->setVar('method', $reload->getDetails());
        
        return $mail;
    }
}

