<?php

class LdapService
{
    private $host = "10.0.52.245";
    private $port = 389;

    public function authenticate($username, $password)
    {
        if (empty($username) || empty($password)) {
            return false;
        }

        // Format URI pour éviter le warning PHP 8.1+
        $ldapconn = @ldap_connect("ldap://" . $this->host . ":" . $this->port);

        if (!$ldapconn) {
            return false;
        }

        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

        // Si l'identifiant contient déjà un domaine (@ ou \), on le laisse tel quel
        if (strpos($username, '@') !== false || strpos($username, '\\') !== false) {
            $ldaprdn = $username;
        } else {
            // Format par défaut Active Directory : username@pf.lab.local
            $ldaprdn = $username . "@pf.lab.local";
        }

        $bind = @ldap_bind($ldapconn, $ldaprdn, $password);

        if ($bind) {
            @ldap_unbind($ldapconn);
            return true;
        }

        return false;
    }
}