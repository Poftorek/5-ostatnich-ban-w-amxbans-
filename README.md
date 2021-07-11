# 5-ostatnich-banów-amxbans-
To plugin 5 ostatnich banów na forum wyciągający, jak sama nazwa wskazuje, 5 ostatnich banów z AmxBansa i pokazujący je w tabeli na forum. Domyślnie wyświetla nick, powód i czas bana oraz przycisk (link) [zmieniający kolor na zielony, kiedy ban wygasł] do szczegółów bana (przenosi na podstronę amxbansa z danym banem). Po najechaniu na wiersz z banem w dymku (atrybut title) podane są rozszerzone informacje - data bana, admin banujący i kiedy ban wygasa. 
Kod HTML tej tabeli znajduje się w szablonach globalnych pod nazwą lastBansOnForumTable oraz lastBansOnForumBody.
Podstawowe stylowanie zawarte jest w pliku styles.txt dołączonym w paczce z pluginem.

# Instalacja
1. Edytuj lastbansonforum.php
Około 133. linijki są 4 puste stringi, w które należy podać dane do połączenia z bazą danych amxbansa.

``` 
$db_host = "";
$db_user = "";
$db_pass = "";
$db_db = "";
```

2. W ustawieniach pluginu zmień adres do amxbansa wg wzoru twojastrona.pl/amxbans (nie dodawaj https:// ani ukośnika na końcu)
3. Zawartość pliku styles.txt wklej do jakiegoś arkusza stylów CSS
4. W szablonie index (strony głównej) dodaj zmienną {$lastBansOnForum} - w jej miejsce zostanie podstawiony kod tabeli ostatnich banów.
