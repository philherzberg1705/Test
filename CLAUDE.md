# BODYWINGS WooCommerce Theme — Claude Code Master Specification

## 0. Zweck und Arbeitsregel

Diese Datei ist die verbindliche zentrale Projektspezifikation für das BODYWINGS WooCommerce WordPress Theme.

Claude Code soll diese Datei bei jeder Arbeit am Projekt berücksichtigen.

Wichtig:
- Anforderungen aus dieser Datei sind verbindlich.
- Neue Änderungen dürfen bestehende Anforderungen nicht unbeabsichtigt brechen.
- Bestehende WordPress-/WooCommerce-Funktionalität soll erhalten bleiben.
- Das Standarddesign von WordPress und WooCommerce darf vollständig überschrieben werden.
- Das Ergebnis soll wie ein eigenständiges Premium-Theme wirken und nicht wie ein Standard-WooCommerce-Shop.
- Keine unnötigen Abhängigkeiten, Libraries oder komplexen Lösungen.
- Erst analysieren und planen, dann programmieren.

---

# 1. Übergeordnetes Designziel

Das Theme soll:

- sehr simpel
- clean
- minimalistisch
- hochwertig
- modern
- übersichtlich
- konsistent
- schnell
- hochwertig animiert

sein.

Die Einfachheit ist ausdrücklich gewünscht.

Nicht gewünscht:
- überladene Layouts
- unnötige UI-Komponenten
- unnötige Farben
- unnötige Schatten
- Standard-WooCommerce-Layouts
- Standard-WordPress-Optik
- Animationen ohne Mehrwert

Die Qualität soll vor allem durch:
- Typografie
- Abstände
- klare Layouts
- starke Produktdarstellung
- konsistente Komponenten
- hochwertige Motion
- saubere Micro-Interactions

entstehen.

---

# 2. Verbindliches Design-System

Das Theme MUSS ein zentrales Design-System besitzen.

Alle wiederkehrenden Werte sollen zentral definiert werden.

Dazu gehören mindestens:
- Farben
- Font-Familien
- Schriftgrößen
- Zeilenhöhen
- Font Weights
- Buttons
- Inputs
- Abstände
- Border Radius
- Container
- Breakpoints
- Shadows
- Animationen
- Easing
- Z-Index

Bevorzugt über CSS Custom Properties und/oder eine zentrale Theme-Konfiguration.

Keine zufälligen Einzelwerte in einzelnen Templates.

## 2.1 Farben

Verbindliche Farbpalette:

Green:
#2D4F1E

Beige:
#F5E6CC

Terrakotta:
#E27D60

Grey:
#4A4A4A

Die Farbpalette muss zentral definiert sein.

Die alten Farben #5A2132 und #EFE9E9 werden NICHT verwendet.

## 2.2 Automatische Font-Farbe

Wenn eine der Theme-Farben als Hintergrund eines Content-Elements gewählt wird, muss die Font-Farbe automatisch angepasst werden.

Regeln:

Green #2D4F1E
→ Font Beige #F5E6CC

Terrakotta #E27D60
→ Font Beige #F5E6CC

Grey #4A4A4A
→ Font Beige #F5E6CC

Beige #F5E6CC
→ Font Green #2D4F1E

Diese Logik muss zentral implementiert werden.

Der Nutzer soll nicht bei jedem Content-Element manuell die Schriftfarbe einstellen müssen.

---

# 3. Typografie

Headline:
Special Gothic Condensed One

Body:
Open Sans

Buttons:
Special Gothic Condensed One

Labels:
Special Gothic Condensed One

## 3.1 Globale Größen

Es müssen feste globale Größen für alle grundlegenden UI-Elemente existieren.

Mindestens:
- H1
- H2
- H3
- H4
- H5
- H6
- Body
- Small Text
- Labels
- Buttons
- Inputs

Die gleichen Elemente müssen im gesamten Theme konsistent aussehen.

Responsive Anpassungen sind erlaubt, müssen aber zentral definiert werden.

---

# 4. Border Radius

Grundregel:

Alles, was nicht über die gesamte Bildschirmbreite geht, soll grundsätzlich abgerundete Ecken besitzen.

Das betrifft unter anderem:
- Cards
- Produktkarten
- Bilder
- Buttons
- Inputs
- Menüs
- Panels
- Filter
- Side Cart
- Modals
- Content-Container
- UI-Komponenten

Border-Radius muss zentral aus dem Design-System kommen.

---

# 5. Content-Elemente

Alle Content-Elemente/Sections sollen eine Einstellung für die Hintergrundfarbe besitzen.

Die Auswahl erfolgt aus der zentralen Theme-Farbpalette.

Wenn eine Hintergrundfarbe gewählt wird, wird die Font-Farbe automatisch anhand der definierten Regeln gewählt.

Die Funktion muss für alle relevanten Content-Elemente konsistent funktionieren.

---

# 6. Header

Der Header muss das BODYWINGS Logo enthalten.

Der Header soll sechs zentrale Icons/Funktionen besitzen:

1. Menü
2. Suche
3. Sprache / Währung
4. Wunschliste
5. Konto
6. Warenkorb

Desktop und Mobile müssen bewusst gestaltet werden.

Mobile ist keine bloße verkleinerte Desktop-Version.

Mögliche/gewünschte mobile Umsetzung:
- Offcanvas-Menü
- Fullscreen Navigation
- hochwertige Übergänge

Icons:
- eindeutig
- touchfreundlich
- keyboardfreundlich
- barrierearm
- animiert
- performant

---

# 7. Globale Wunschliste

Es soll eine allgemeine Wunschlistenfunktion geben.

Funktionen:
- Produkt hinzufügen
- Produkt entfernen
- Status anzeigen
- Wishlist Icon auf Produktkarten
- Wishlist Icon im Header
- Wunschlisten-Seite
- AJAX
- responsive
- animierte Micro-Interactions

Für eingeloggte Benutzer soll die Wunschliste dauerhaft gespeichert werden.

Für nicht eingeloggte Benutzer soll, sofern technisch sinnvoll, eine lokale Speicherung möglich sein.

Die Funktion soll möglichst nicht von einem separaten Wishlist-Plugin abhängig sein.

---

# 8. Produktkarten

Produktkarten sollen bewusst extrem simpel aufgebaut sein.

Grundstruktur:

1. Produktbild
2. Varianten-Auswahl
3. Produkttitel
4. Preis

Keine unnötigen Informationen auf der Card.

## 8.1 Varianten-Auswahl

Die Varianten-Auswahl befindet sich direkt zwischen Bild und Titel.

Wenn eine Variante ausgewählt wird:
- Produktbild ändern
- möglichst ohne Page Reload
- AJAX bzw. clientseitige Aktualisierung sinnvoll verwenden
- Wechsel animieren

## 8.2 Attributauswahl

Im Backend soll ein Bereich "BodywingsTheme" existieren.

Dort soll konfigurierbar sein:
- welche WooCommerce-Attribute für Produktkarten verwendet werden
- welche Attribute für Filter verwendet werden
- welche Attribute auf der Produktseite visuell dargestellt werden

---

# 9. Attributbilder

WooCommerce-Attributwerte sollen zentral mit Bildern gepflegt werden können.

Beispiel:

Farbe:
- Grün → Bild
- Beige → Bild
- Terrakotta → Bild

Die Attributbilder sollen zentral gepflegt werden und wiederverwendbar sein.

Verwendung:
- Produktkarten
- Varianten-Auswahl
- Produktdetailseite
- Filter
- weitere sinnvolle Stellen

Nicht unnötig pro Produkt dieselben Attributbilder duplizieren.

---

# 10. Zweites Produktbild / Hover

Wenn ein Produkt ein zweites Produktbild besitzt:

Beim Hover über die Produktkarte soll das zweite Bild erscheinen.

Der Wechsel soll:
- weich
- modern
- hochwertig
- performant

animiert werden.

Wenn kein zweites Bild existiert:
- normales Produktbild behalten.

Auf Touch-Geräten darf kein störender Hover-Ersatz entstehen.

---

# 11. Transparente Produktbilder

Wenn ein Produktbild transparente Pixel bzw. einen transparenten Hintergrund besitzt, soll automatisch ein Drop-Shadow verwendet werden.

Bevorzugt:
CSS filter: drop-shadow()

Der Schatten soll der tatsächlichen Produktform folgen.

Kein einfacher rechteckiger Schatten um das gesamte Bild.

Der Effekt:
- dezent
- hochwertig
- performant

---

# 12. Produktdetailseite

Die Produktdetailseite soll eine individuelle hochwertige WooCommerce-UI besitzen.

Berücksichtigen:

- Breadcrumb
- Produktbilder
- Produktname
- Kategorie
- Bewertungen
- Kurzbeschreibung
- Preis
- Varianten
- Attributbilder
- Mengenwahl
- Add to Cart
- Wishlist
- Versandinformationen
- zusätzliche Informationen
- Produktbeschreibung
- Bewertungen
- ähnliche Produkte

Die Seite darf nicht wie ein Standard-WooCommerce-Template aussehen.

Variantenwechsel soll möglichst ohne vollständigen Reload erfolgen.

Bildwechsel soll hochwertig animiert werden.

---

# 13. Produktfilter

Ein moderner WooCommerce-Filter ist erforderlich.

Filter möglichst AJAX-basiert.

Mögliche Filter:
- Kategorien
- Attribute
- Attributbilder
- Preis
- Verfügbarkeit
- weitere WooCommerce-Attribute

Attributbilder müssen visuell im Filter verwendet werden können.

Desktop:
- klarer Filterbereich

Mobile:
- eigenes Filter-Offcanvas/Panel

Filteränderungen:
- kein vollständiger Reload
- Loading State
- Animation
- URL-State möglichst erhalten
- Back/Forward sinnvoll unterstützen
- performant

---

# 14. Suche

Header-Suche mit moderner UX.

Klick auf Such-Icon:
→ Suchbereich öffnet sich.

Live-Suche:
- Produktname
- Produktbild
- Preis
- Varianten
- optional SKU

AJAX-basiert, wenn sinnvoll.

Suchergebnisse animiert anzeigen.

Enter:
→ vollständige Suchergebnisseite.

Keine unnötigen API-/Serveranfragen.

---

# 15. Globales AJAX-System

AJAX soll für geeignete Interaktionen konsequent verwendet werden.

Mindestens prüfen:

- Wishlist
- Add to Cart
- Mini Cart
- Mengenänderung
- Cart Remove
- Filter
- Suche
- Varianten
- Login
- Registrierung
- Passwort vergessen

AJAX ist kein Selbstzweck.

Wenn ein normaler Request technisch sinnvoller, sicherer oder performanter ist, soll dieser verwendet werden.

---

# 16. Side Cart / Mini Cart

Nach Add to Cart soll ein moderner Side Cart bzw. Mini Cart erscheinen können.

Inhalt:
- Produktbild
- Produktname
- Variante
- Preis
- Menge
- Entfernen
- Zwischensumme
- Warenkorb
- Checkout

Der Side Cart soll:
- animiert
- responsive
- performant
- barrierearm

sein.

---

# 17. Login / Registrierung / Passwort vergessen

Login, Registrierung und Passwort vergessen befinden sich auf einer gemeinsamen Seite bzw. in einem gemeinsamen Auth-Bereich.

Ansichten:
- Login
- Registrierung
- Passwort vergessen

Wechsel über Buttons/Tabs.

Wechsel:
- ohne unnötigen Page Reload
- animiert
- flüssig
- hochwertig

WordPress-/WooCommerce-Authentifizierung korrekt verwenden.

Fehler und Validierungen direkt im jeweiligen Bereich anzeigen.

---

# 18. Währungssystem

Euro (€) ist die Standard- und Basiswährung.

Das vorhandene WooCommerce-Mehrwährungsplugin soll unterstützt werden.

Eine öffentliche Wechselkurs-API soll verwendet werden.

Wechselkurse:
- automatisch alle 1 Stunde aktualisieren
- EUR als Basis
- cachen
- nicht bei jedem Seitenaufruf API anfragen
- WordPress Cron oder geeignete Hintergrundroutine
- letzten erfolgreichen Kurs bei API-Ausfall verwenden
- API austauschbar halten

Die Lösung muss mit:
- Produkten
- Varianten
- Warenkorb
- Checkout

kompatibel sein.

Die API darf nicht unnötig hart verdrahtet werden.

API-Schicht abstrahieren.

---

# 19. Footer

Der Footer benötigt ein Newsletter-Modul.

Spätere Integration mit Brevo.

Das Frontend muss bereits dafür vorbereitet werden.

Anforderungen:
- E-Mail
- Validierung
- Erfolgsstatus
- Fehlerstatus
- DSGVO-relevante Einwilligung
- saubere Provider-Abstraktion
- später Brevo API anschließbar

Das Theme darf nicht vollständig von Brevo abhängig sein.

---

# 20. Loading States

Alle relevanten AJAX-Interaktionen benötigen Loading States.

Beispiele:
- Filter
- Suche
- Wishlist
- Warenkorb
- Varianten
- Login
- Registrierung

Mögliche Darstellung:
- Skeleton
- Spinner
- Button Loading
- Placeholder

Keine unnötigen Layout Shifts.

Loading States müssen zum Design-System passen.

---

# 21. Motion / Animation

Das Theme soll stark hochwertig animiert sein, obwohl das Design minimalistisch bleibt.

Animationen können verwendet werden für:
- Header
- Navigation
- Offcanvas
- Suche
- Produktkarten
- Produktbildwechsel
- Wishlist
- Warenkorb
- Filter
- Buttons
- Formulare
- Auth Tabs
- Page Transitions
- Scroll Animations
- Micro Interactions

Animationen müssen einen sinnvollen Zweck haben.

Keine Animation um der Animation willen.

---

# 23. Animationstechnologie

Nicht GSAP und Motion überall gleichzeitig verwenden.

Regeln:

Einfache Hover-/Transition-Effekte:
→ bevorzugt CSS

Komplexe Sequenzen / Scroll:
→ GSAP

UI-State-Transitions:
→ Motion, sofern sinnvoll

Es soll kein unnötiges paralleles Animationssystem entstehen.

Zentrales Motion-System definieren:
- fast
- normal
- slow
- easing
- ggf. spring

Animationen müssen auch auf schwächeren Mobilgeräten performant bleiben.

---

# 24. Responsive Design

Das Theme muss vollständig responsive sein:

- Desktop
- Laptop
- Tablet
- Mobile

Layouts müssen für die jeweilige Bildschirmgröße sinnvoll gestaltet werden.

Nicht einfach Desktop verkleinern.

Besonders prüfen:
- Header
- Navigation
- Produktkarten
- Produktseite
- Filter
- Side Cart
- Suche
- Formulare
- Animationen

Touch Targets müssen ausreichend groß sein.

---

# 25. Accessibility

Accessibility ist eine harte Anforderung.

Berücksichtigen:
- semantisches HTML
- Tastaturnavigation
- sichtbare Focus States
- Screenreader
- korrekte Labels
- ausreichender Farbkontrast
- sinnvolle ARIA-Attribute
- kein unnötiges ARIA
- Escape zum Schließen
- Fokusmanagement bei Modals/Offcanvas
- Touch Targets
- prefers-reduced-motion

Bei prefers-reduced-motion:
- Animationen reduzieren/deaktivieren
- Funktionalität vollständig erhalten

Keine wichtige Information darf nur durch Animation vermittelt werden.

---

# 26. Performance

Performance ist eine harte Vorgabe.

Trotz hochwertiger Animation muss das Theme schnell bleiben.

Berücksichtigen:
- wenig JavaScript
- wenig CSS
- keine unnötigen Libraries
- Code Splitting, wo sinnvoll
- Lazy Loading
- responsive Images
- WebP/AVIF
- Caching
- minimale API Requests
- keine unnötigen WooCommerce Assets
- keine unnötigen Page Reloads
- Layout Shift vermeiden
- Core Web Vitals
- GPU-freundliche Animationen
- keine Memory-Leaks bei Animationen/Event Listenern

Nur notwendige Assets laden.

---

# 27. SEO

SEO-freundliche Theme-Architektur.

Berücksichtigen:
- semantisches HTML
- korrekte Heading-Hierarchie
- Breadcrumbs
- saubere Links
- Produktdaten
- WooCommerce Schema nicht kaputt machen
- SEO Plugins unterstützen
- Rank Math nicht unnötig überschreiben
- keine doppelten H1
- Alt-Texte
- crawlbare Produktlinks

Das Theme soll kein eigenes SEO-Plugin werden.

---

# 28. Plugin-Kompatibilität

Besonders berücksichtigen:
- WordPress
- WooCommerce
- bestehendes WooCommerce Mehrwährungsplugin
- WPML
- Rank Math
- Brevo
- Gutenberg

Elementor nur dann speziell integrieren, wenn es in der konkreten Projektumgebung benötigt wird.

Grundregel:

Optionales Plugin vorhanden:
→ Integration aktivieren.

Plugin nicht vorhanden:
→ Theme muss weiterhin sinnvoll funktionieren.

---

# 29. BodywingsTheme Backend

Es soll im WordPress Backend einen eigenen Bereich geben:

BODYWINGS Theme

Mögliche Bereiche:
- Dashboard
- Allgemein
- Design
- Farben
- Typografie
- Header
- Footer
- Produktkarten
- Produktseite
- Shop
- Filter
- Wunschliste
- Währungen
- Newsletter
- WooCommerce
- Performance
- Animationen

Nicht jede Kleinigkeit als Option anbieten.

Das Theme soll mit guten Defaults funktionieren.

---

# 30. Architektur

Keine riesige functions.php mit sämtlicher Logik.

Sauber modular trennen.

Beispielhafte Bereiche:
- Core
- Admin
- WooCommerce
- Components
- Templates
- Assets
- CSS
- JavaScript
- AJAX
- Integrations
- Utilities

Claude soll die finale Struktur nach Analyse des konkreten Repositories bestimmen und begründen.

---

# 31. WooCommerce-Grundregel

Wenn WooCommerce eine Funktion bereits sauber bereitstellt:
→ vorhandene WooCommerce-Funktion verwenden.

Nicht unnötig WooCommerce intern nachbauen.

Templates/Hooks dürfen überschrieben werden, wenn es für das Design notwendig ist.

Funktionalität muss erhalten bleiben.

---

# 32. Sicherheit

Berücksichtigen:
- Nonces
- Capability Checks
- Sanitization
- Validation
- Escaping
- sichere AJAX Requests
- sichere REST Requests
- keine sensiblen Daten im Frontend
- keine ungefilterten User Inputs ausgeben

---

# 33. Codequalität

Code muss:
- sauber
- modular
- wartbar
- verständlich
- dokumentiert
- WordPress-konform
- WooCommerce-konform
- erweiterbar

sein.

Keine unnötigen Dependencies.

Keine unnötigen Abstraktionen.

Keine Copy-Paste-Komponenten, wenn eine zentrale Komponente möglich ist.

---

# 34. Claude Code Workflow — VERBINDLICH

Claude darf NICHT sofort mit dem Programmieren beginnen.

## Phase 1 — Analyse

Zuerst Repository untersuchen:

1. Dateien
2. WordPress-Version
3. WooCommerce-Version
4. Plugins
5. Theme-Struktur
6. Build-System
7. Assets
8. bestehende Funktionen
9. bestehende Datenstrukturen
10. mögliche Konflikte

## Phase 2 — Architektur

Danach planen:

1. Theme-Architektur
2. Dateistruktur
3. Design-System
4. Motion-System
5. Backend
6. WooCommerce Integration
7. Attributbilder
8. AJAX
9. Integrationen
10. Performance

## Phase 3 — Implementierungsplan

Reihenfolge sinnvoll festlegen.

Empfohlene Reihenfolge:

1. Foundation
2. Design System
3. Theme Shell
4. Header
5. Footer
6. WooCommerce Foundation
7. Product Cards
8. Product Page
9. Shop
10. Filter
11. Search
12. Cart
13. Wishlist
14. Authentication
15. Currency
16. Newsletter
17. Animation
18. Accessibility
19. Performance
20. Testing

## Phase 4 — Entwicklung

Modulweise implementieren.

Nach jedem größeren Modul:
- Code prüfen
- Fehler suchen
- PHP prüfen
- JS prüfen
- CSS prüfen
- WooCommerce prüfen
- Accessibility prüfen
- Responsive prüfen
- Performance prüfen

## Phase 5 — Finaler Test

Mindestens testen:

- Desktop
- Tablet
- Mobile
- Shop
- Produkt
- Varianten
- Cart
- Checkout
- Login
- Registrierung
- Passwort vergessen
- Wishlist
- Suche
- Filter
- Währungen
- Header
- Footer
- Newsletter
- Accessibility
- prefers-reduced-motion
- Performance
- Console Errors
- PHP Errors

---

# 35. Entscheidungsregeln

Bei mehreren technischen Möglichkeiten:

1. Wartbarkeit
2. WordPress-/WooCommerce-Kompatibilität
3. Performance
4. Accessibility
5. Sicherheit
6. Erweiterbarkeit
7. UX
8. Design

priorisieren.

Keine unnötig komplizierte Lösung.

Wenn eine wichtige Entscheidung nicht eindeutig ist:
- Optionen analysieren
- beste Lösung auswählen
- Entscheidung kurz begründen

Keine stillschweigenden Architekturentscheidungen, die später schwer rückgängig zu machen sind.

---

# 36. Prioritäten bei Konflikten

Wenn Anforderungen miteinander kollidieren:

1. Funktionalität
2. Sicherheit
3. Accessibility
4. Performance
5. Responsive Design
6. Design-System
7. UX
8. Animation
9. Komfortfunktionen

Animation darf niemals:
- Funktionalität zerstören
- Accessibility verschlechtern
- Performance deutlich verschlechtern
- Navigation erschweren

---

# 37. Definition of Done

Eine Funktion ist erst fertig, wenn sie:

- funktioniert
- responsive funktioniert
- zum Design-System passt
- barrierearm ist
- performant ist
- sinnvoll animiert ist
- keine Console Errors erzeugt
- keine PHP Errors erzeugt
- WooCommerce nicht beschädigt
- Desktop getestet wurde
- Mobile getestet wurde
- Tablet sinnvoll berücksichtigt wurde
- Loading State besitzt, wenn AJAX verwendet wird

---

# 38. Zukunftssicherheit

Architektur so bauen, dass später einfach ergänzt werden können:

- weitere WooCommerce Funktionen
- weitere Zahlungsarten
- weitere Versandarten
- weitere Währungen
- Brevo
- WPML
- zusätzliche Filter
- weitere Produktdarstellungen
- weitere Animationen
- weitere Content-Elemente
- weitere Backend-Einstellungen

Keine unnötig starre Architektur.

---

# 39. Visuelles Endziel

Das fertige Theme soll sich anfühlen wie:

MINIMAL
+
PREMIUM
+
MODERN
+
CLEAN
+
HOCHWERTIGE MOTION

Die Benutzer sollen eine einfache Oberfläche bekommen, die trotzdem hochwertig und lebendig wirkt.

Die wichtigsten visuellen Werkzeuge sind:

- Green #2D4F1E
- Beige #F5E6CC
- Terrakotta #E27D60
- Grey #4A4A4A
- Special Gothic Condensed One
- Open Sans
- einheitliche Größen
- einheitliche Rundungen
- klare Abstände
- starke Produktbilder
- dezente Drop Shadows bei transparenten Produktbildern
- hochwertige Animationen
- Micro Interactions

Nicht gewünscht:

- Standard WordPress
- Standard WooCommerce
- überladene UI
- unnötige Effekte
- langsame Animationen
- inkonsistente Größen
- inkonsistente Farben
- unnötige Plugin-Abhängigkeiten

---

# ENDE DER BODYWINGS THEME MASTER SPECIFICATION
