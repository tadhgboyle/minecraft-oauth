<?php

namespace Aberdeener\MinecraftOauth\Exceptions;

class XtxsTokenRetrievalException extends MinecraftOauthException
{
    public static function withXErr(int $xErr): self
    {
        $message = "XErr: $xErr, ";

        switch ($xErr) {
            case 2148916233:
                $message .= "The account doesn't have an Xbox account";
                break;
            case 2148916235:
                $message .= 'The account is from a country where Xbox Live is not available/banned';
                break;
            case 2148916236:
            case 2148916237:
                $message .= 'The account needs adult verification on Xbox page';
                break;
            case 2148916238:
                $message .= 'The account is a child (under 18) and cannot proceed unless the account is added to a Family by an adult';
                break;
            default:
                $message = "An unknown error occurred, XErr: $xErr";
                break;
        }

        return new self($message);
    }
}
