# MASTER CAPABILITY MATRIX

## But

Définir les capacités attendues par OS/famille sans confondre support
prévu et détection réelle. Cette matrice est une **cible d'architecture
V3.4**, à valider par tests de compatibilité.

  --------------------------------------------------------------------------------------------
  Capability        Debian/Ubuntu                     Alma/Rocky           Règle WCP
  ----------------- --------------------------------- -------------------- -------------------
  Apache/Nginx      adapter                           adapter              détecté/installé,
                                                                           jamais supposé

  PHP-FPM           adapter                           adapter              versions via
                                                                           repository strategy

  Firewall          nftables/firewalld selon hôte     firewalld/nftables   une source de
                                                      selon hôte           vérité active

  MAC               AppArmor fréquent                 SELinux fréquent     capability détectée

  Network config    networkd/NetworkManager/Netplan   NetworkManager       adapter détecté
                    selon hôte                        principalement       

  Time sync         chrony/timesyncd selon hôte       chrony/timesyncd     adapter
                                                      selon hôte           

  DNS authoritative BIND + futurs adapters            BIND + futurs        moteur
                                                      adapters             interchangeable

  Containers        Docker/LXC selon installation     Docker/LXC selon     optionnel
                                                      installation         
  --------------------------------------------------------------------------------------------

## Règle

Le frontend interroge `/capabilities` (ou contrat équivalent) ; il ne
déduit jamais une capacité uniquement du nom de distribution.
