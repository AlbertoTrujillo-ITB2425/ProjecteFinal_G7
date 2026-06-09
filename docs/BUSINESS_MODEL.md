# 📊 Model de Negoci — CyberPyme G7

Anàlisi de mercat, estratègia de preus i model de negoci del projecte **CyberPyme G7**, un servei de monitoratge i auditoria de ciberseguretat per a petites i mitjanes empreses, desenvolupat com a projecte final del CFGS ASIXc2 a l'Institut Tecnològic de Barcelona.

---

## Taula de continguts

- [El problema](#el-problema)
- [Mercat objectiu](#mercat-objectiu)
- [Mida del mercat](#mida-del-mercat)
- [Estratègia de preus](#estratègia-de-preus)
- [Fonts d'ingressos](#fonts-dingressos)
- [Projeccions financeres](#projeccions-financeres)
- [Anàlisi competitiva](#anàlisi-competitiva)
- [Casos d'ús reals](#casos-dús-reals)
- [Diferenciadors clau](#diferenciadors-clau)
- [Bibliografia](#bibliografia)

---

## El problema

**El 70% dels ciberatacs a Espanya tenen com a objectiu les PYME**, no les grans corporacions. Malgrat això:

- Només el **14%** de les PYME espanyoles disposen de mesures de ciberseguretat adequades
- El cost mitjà d'una bretxa de dades per a una PYME és de **€133.000**
- El **60% de les empreses petites** tanquen en els 6 mesos posteriors a un ciberatac
- El **87% de les PYME** no té departament d'IT propi
- Les solucions de seguretat empresarials tradicionals costen entre **€500 i €2.000/mes**, inassolibles per a la majoria de PYME

A Espanya, l'INCIBE va gestionar **97.348 incidents de ciberseguretat el 2024**, un 16,6% més que el 2023, dels quals **31.540 van afectar empreses** — un 43,2% més que l'any anterior. A Catalunya, l'Agència de Ciberseguretat va gestionar 6.544 incidents el 2025, amb 9.100 milions d'intents d'atac detectats — un 32% més que el 2024.

**El context és clar: les PYME necessiten protecció professional i assequible.**

---

## Mercat objectiu

### Principal: PYME sense departament d'IT
- **Mida**: 1–50 treballadors | **Facturació**: €100K–€2M anuals
- Presència digital: web, comerç electrònic o serveis online
- Sectors: comerç, restauració, serveis professionals, salut, immobiliàries, e-commerce

### Secundari: PYME que busquen optimitzar costos
- **Mida**: 20–250 treballadors
- Paguen entre €3.000 i €8.000/mes a consultors d'IT
- Sistemes de seguretat obsolets, sense monitoratge 24/7

---

## Mida del mercat

| Territori | Total PYME | Sense IT propi | Mercat objectiu | Valor anual estimat |
|---|---|---|---|---|
| **Espanya** | 2,9M | 1,57M | 540.000 | €194,4M |
| **Catalunya** | 557 empreses de ciberseg. | — | — | €1.473M (sector) |
| **UE** | 23M | 12,8M | 4,4M | €1,58B |

**Factors de creixement**:
- CAGR de ciberseguretat per a PYME: **15,2%** (2024–2030)
- Directiva NIS2: regulació de la UE que obliga les PYME a implementar seguretat
- Requisits d'assegurances cibernètiques, tendències de treball remot
- Programa **Pyme Cibersegura** (Govern d'Espanya / FEDER): subvencions de fins a €4.494 per empresa

---

## Estratègia de preus

### Plans de subscripció

| Pla | €/Mes | Anual (20% dto.) | Objectiu |
|---|---|---|---|
| **Bàsic** | €29,99 | €287,90 | 1–10 treballadors, un sol lloc web |
| **Professional** | €59,99 | €575,90 | 10–50 treballadors, multi-lloc |
| **Business** | €99,99 | €959,90 | 50–250 treballadors, compliment normatiu |
| **Enterprise** | A mida | A mida | +250 treballadors |

> ⚠️ **Nota del projecte:** Els preus en criptomoneda (EURC/USDC via Solana) del document original s'han mantingut com a opció futura, però el model actual del projecte G7 opera en euros convencionals via transferència bancària o Bizum, sense blockchain integrada. La infraestructura de Solana no forma part del desplegament actual a AWS.

### Costos tradicionals IT vs. CyberPyme G7

| Despesa | Tradicional | CyberPyme G7 | Estalvi |
|---|---|---|---|
| Consultor IT (retainer) | €3.000–€6.000/mes | €0 | €3.000–€6.000 |
| Programari de seguretat | €200–€800/mes | Inclòs | €200–€800 |
| Eines de monitoratge | €150–€400/mes | Inclòs | €150–€400 |
| Auditories de compliment | €2.000–€5.000/any | €99/informe | €1.800–€4.900 |
| **Total mensual** | **€3.500–€7.200** | **€29,99–€99,99** | **€3.400–€7.100** |

---

## Fonts d'ingressos

1. **Quotes de subscripció** (85% dels ingressos) — MRR, pagaments anuals, upsells
2. **Serveis professionals** (10%) — Auditoria inicial €299, informes de compliment €99, retainer d'incidents €199/mes
3. **Comissions de partners** (5%) — Referidors d'hosting (15%), venda de maquinari (10%), llicències d'API

### Economia unitària (Pla Professional)
- Ingressos mensuals: €59,99
- Costos de servidor AWS: €2,50/client
- Costos de suport: €5,00/client
- **Marge brut: €52,48 (87,5%)**

### Anàlisi de punt d'equilibri
- Costos fixos mensuals: €865/mes (AWS €150, suport €500, màrqueting €200, misc €15)
- **Punt d'equilibri: 17 clients | Previsió: Mes 3–4**

---

## Projeccions financeres

| Any | Clients | MRR | Ingressos anuals | Benefici net | Marge |
|---|---|---|---|---|---|
| 1 | 120 | €5.999 | €71.988 | €28.795 | 40% |
| 2 | 350 | €17.497 | €209.964 | €125.978 | 60% |
| 3 | 800 | €39.992 | €479.904 | €335.933 | 70% |
| 4 | 1.500 | €74.985 | €899.820 | €719.856 | 80% |
| 5 | 2.500 | €124.975 | €1.499.700 | €1.274.745 | 85% |

*Supòsits: 15% de churn mensual, 8% de creixement mensual, marges millorant amb l'escala.*

---

## Anàlisi competitiva

### Posicionament al mercat

```
              Preu alt
                  │
       Akamai ●   │       (Empresa gran)
                  │
    Consultors ●  │● SiteLock
                  │
                  │● Sucuri
                  │● Cloudflare Pro
  CyberPyme G7 ●  │     ← La nostra posició
                  │
                  │● DIY/Gratuït
           Baix   │
                  └──────────────────────
                  Baixes ← Funcions → Altes
```

### Matriu de funcionalitats

| Funcionalitat | **CyberPyme G7** | **Sucuri** | **Cloudflare Pro** | **SiteLock** | **Consultor IT** |
|---|---|---|---|---|---|
| **Preu/mes** | €29,99–€99,99 | €16,67 | €20 | €99,99 | €3.000–€6.000 |
| **Temps de configuració** | < 30 min | 1–2 dies | 30 min | 1 setmana | 1 mes |
| **Sense coneixements IT** | ✅ | ⚠️ | ❌ | ✅ | ❌ |
| **Monitoratge 24/7** | ✅ | ✅ | ⚠️ | ✅ | ❌ |
| **Conforme RGPD** | ✅ | ❌ (EUA) | ✅ | ⚠️ | ✅ |
| **Suport en català/castellà** | ✅ | ❌ | ❌ | ⚠️ | ✅ |
| **SIEM integrat (Wazuh)** | ✅ | ❌ | ❌ | ❌ | Depèn |
| **IDS/IPS (Snort)** | ✅ | ❌ | ❌ | ❌ | Depèn |
| **Informe de vulnerabilitats** | ✅ | ✅ | ❌ | ✅ | ✅ |
| **Codi obert** | ✅ | ❌ | ❌ | ❌ | ❌ |

### Sobre les empreses competidores

**Sucuri** (GoDaddy, EUA) és el líder en protecció de llocs WordPress. Ofereix WAF i CDN a €16,67/mes, però no disposa de SIEM ni IDS propis, i els seus servidors estan als EUA, cosa que genera friccions amb el RGPD europeu.

**Cloudflare** (EUA, cotitza al NYSE: NET) és el major proveïdor de CDN i protecció DDoS del món, amb més de 200 punts de presència global. El seu pla Pro (€20/mes) inclou WAF bàsic, però no ofereix monitoratge de seguretat interna ni alertes personalitzades per a la infraestructura del client.

**SiteLock** (EUA) ofereix escaneig de vulnerabilitats i reparació automatitzada a €99,99/mes. Té bona reputació al mercat americà però presència limitada a Espanya i sense suport en català o castellà.

**Akamai** (NASDAQ: AKAM) és el referent empresarial amb solucions de seguretat des de €500/mes. Orientat exclusivament a grans corporacions, completament fora de l'abast econòmic d'una PYME.

**Consultors IT locals (Espanya)** cobren entre €3.000 i €6.000/mes per serveis equivalents. Representen el competidor directe més comú per a les PYME espanyoles, però el seu cost és prohibitiu.

---

## Casos d'ús reals

> Els casos següents combinen situacions reals documentades per l'INCIBE i la proposta del projecte, adaptades al context de les PYME catalanes/espanyoles.

---

### Cas 1 — Farmàcia familiar (15 treballadors, Barcelona)

**Situació real (INCIBE 2024):** L'any 2024, es van registrar **2.122 incidents relacionats amb botigues online fraudulentes** i milers d'atacs a llocs web de comerç electrònic de PYME. Una farmàcia amb tenda online va patir un atac de tipus *defacement* (modificació del web) i *phishing* als seus clients.

**Problema concret:**
- Web hackejada el 2024, cost de neteja: €2.500
- Caiguda del 30% de les vendes online durant 2 setmanes
- Risc de multa RGPD de fins a €50.000 per exposició de dades de salut

**Solució amb CyberPyme G7 — Pla Professional (€59,99/mes):**
- Snort (S11) bloquejant 50+ intents d'atac diaris
- Wazuh (S7) detectant modificacions de fitxers en temps real
- Postfix (S10) enviant alertes automàtiques per correu
- Informes de compliment RGPD mensuals via `socmail.php`

**ROI:** Cost anual €720 vs €52.500 en pèrdues potencials = **ROI del 7.191%**

---

### Cas 2 — Gestoría comptable (10 treballadors, 200 clients, Sabadell)

**Situació real:** Segons l'INCIBE, el **phishing** va ser el segon incident més freqüent el 2024 (21.571 casos). Moltes gestories han vist dades de clients robades via correus fraudulents simulant l'Agència Tributària.

**Problema concret:**
- Dades de clients robades via phishing simulant l'AEAT
- Pèrdua de 5 clients, reducció d'ingressos de €18.000/any
- Assegurança cibernètica: €4.000/any

**Solució amb CyberPyme G7 — Pla Business (€99,99/mes):**
- Wazuh SIEM (S7) detectant phishing en temps real via anàlisi de logs
- OpenLDAP (S6) amb control d'accés per rols (els comptables no accedeixen a dades d'altres)
- Snort (S11) bloquejant connexions a dominis de phishing coneguts
- Informes de compliment per a l'acreditació professional

**Resultats als 6 mesos:** 0 incidents, 3 nous clients atrets per la seguretat demostrada, assegurança reduïda a €2.400/any

---

### Cas 3 — Restaurant amb comandes online (12 treballadors, Gràcia, BCN)

**Situació real:** L'INCIBE va registrar **357 atacs de ransomware** el 2024 i centenars d'atacs DDoS a webs de PYME. Restaurants amb comandes online en hora punta són objectius habituals.

**Problema concret:**
- Atac DDoS durant el servei de dinar: pèrdua de €500 en comandes en 2 hores
- Sense monitoratge, l'atac no es va detectar fins al cap de 45 minuts
- Preocupacions de compliment PCI-DSS per als pagaments amb targeta

**Solució amb CyberPyme G7 — Pla Bàsic (€29,99/mes):**
- Cloudflare integrat per a protecció DDoS capa 3/4/7
- Snort (S11) amb alerta immediata via Postfix (S10)
- Dashboard Grafana (S8) amb semàfor verd/vermell visible a temps real
- Llista de compliment PCI-DSS mensual

**ROI:** Cost anual €360 vs €3.200 en pèrdues potencials = **ROI del 789%**

---

### Cas 4 — Startup de RRHH (35 treballadors, Barcelona)

**Situació real:** Moltes startups tecnològiques catalanes acumulen costos d'eines SaaS de seguretat i monitoratge sense integrar-les. El sector tecnològic català va créixer un 18,4% en volum de negoci el 2025 (ACCIÓ), però moltes startups segueixen pagant per eines fragmentades.

**Problema concret:**
- Datadog: €800/mes + Cloudflare: €200/mes + seguretat: €300/mes + DevOps extern: €1.500/mes
- **Total: €2.800/mes** en eines no integrades

**Solució amb CyberPyme G7 — Pla Enterprise (€299/mes):**
- Substitueix Datadog (Grafana S8 + Wazuh S7)
- Substitueix Cloudflare Pro (Snort S11 + protecció perimetral S0)
- Substitueix eines de seguretat fragmentades (stack integrat Docker)
- **Estalvi: €2.501/mes (€30.012/any)**

---

### Cas 5 — Clínica dental amb portal de pacients (9 treballadors, Terrassa)

**Situació real:** Les dades de salut (categoria especial del RGPD) estan subjectes a les multes més elevades: fins a €20.000.000 o el 4% de la facturació global. El 2024, diverses clíniques espanyoles van ser investigades per l'AEPD per manca de mesures tècniques adequades.

**Problema concret:**
- Auditoria RGPD: cost €5.000
- Risc de multa per exposició de dades de salut: €20.000–€200.000
- Sense registre d'accessos als historials dels pacients

**Solució amb CyberPyme G7 — Pla Professional (€59,99/mes):**
- OpenLDAP (S6) amb control d'accés per historial de pacient
- Wazuh (S7) amb registre d'accés a dades sensibles (auditoria RGPD)
- Backups xifrats AES-256 del volum MariaDB a AWS S3
- Informe mensual de compliment RGPD

**Valor:** Cost anual €720 vs €5.000+ en costos de compliment + risc de multa il·limitat

---

## Diferenciadors clau

1. **Optimitzat per a PYME**: Dissenyat per a empreses sense departament IT, amb interfície `socmail.php` intuïtiva
2. **Stack de codi obert**: Nginx, MariaDB, OpenLDAP, Wazuh, Snort, Grafana — sense llicències propietàries
3. **Centrat en la UE**: RGPD natiu, suport en català i castellà, compliment NIS2
4. **Sense dependència del proveïdor**: Desplegament en qualsevol VPS o instància cloud amb `docker compose up -d`
5. **Preu transparent**: Sense costos ocults, sense compromís mínim

---

## Bibliografia

1. **INCIBE** (2025). *Balance de Ciberseguridad 2024*. Instituto Nacional de Ciberseguridad de España. Recuperat de: https://www.incibe.es/incibe/sala-de-prensa/incibe-presenta-su-balance-de-ciberseguridad-2024-con-mas-de-97000-incidentes

2. **INCIBE** (2025). *Infografia: Balance de Ciberseguridad INCIBE 2024*. Recuperat de: https://www.incibe.es/sites/default/files/Comunicaci%C3%B3n_2025/Infograf%C3%ADa_BalanceCiberseguridad_INCIBE_2024_web.pdf

3. **INCIBE** (2024). *Las principales vulnerabilidades de una PYME en materia de ciberseguridad*. Recuperat de: https://www.incibe.es/empresas/blog/las-principales-vulnerabilidades-de-una-pyme-en-materia-de-ciberseguridad

4. **ACCIÓ / Agència de Ciberseguretat de Catalunya** (2025). *La ciberseguretat a Catalunya — Actualització 2025*. Generalitat de Catalunya. Recuperat de: https://www.accio.gencat.cat/ca/serveis/banc-coneixement/cercador/BancConeixement/eic-la-ciberseguretat-a-catalunya

5. **Agència de Ciberseguretat de Catalunya** (2026). *La ciberseguretat a Catalunya es tensiona: els incidents gairebé es dupliquen el 2025*. Recuperat de: https://www.metadata.cat/noticia/6283/ciberseguretat-catalunya-tensiona-incidents-dupliquen-2025

6. **Pentesting Team** (2026). *Ciberataques en España 2025: cifras INCIBE y claves para la protección*. Recuperat de: https://pentestingteam.com/blog/ciberataques-espana-2025-incibe-claves/

7. **Cambra de Comerç de Barcelona** (2024). *La importància de la Ciberseguretat a les empreses*. Recuperat de: https://cambrabcn.org/la-importancia-de-la-ciberseguretat-a-les-empreses/

8. **Govern d'Espanya / FEDER** (2025). *Programa Pyme Cibersegura 2025*. Cámara de Comercio de Alicante. Recuperat de: https://www.camaralicante.com/innovacion-y-tic/pymecibersegura/pymecibersegura-2025/

9. **Govern d'Espanya** (2021–2025). *Plan de Digitalización de PYMEs 2021–2025 — Programa Activa Ciberseguridad*. Recuperat de: http://espanadigital.gob.es/medida/plan-de-digitalizacion-de-pymes-2021-2025

10. **Libertia** (2026). *Ciberseguridad para Pymes: Protege tu negocio en 2025*. Recuperat de: https://libertia.es/ciberseguridad-para-pymes/

11. **Sucuri** (GoDaddy). *Sucuri Website Security Platform*. Recuperat de: https://sucuri.net/pricing/

12. **Cloudflare, Inc.** (2025). *Cloudflare Pro Plan — Features and Pricing*. Recuperat de: https://www.cloudflare.com/plans/

13. **Akamai Technologies** (2025). *Enterprise Security Solutions*. Recuperat de: https://www.akamai.com/solutions/security

14. **SiteLock** (2025). *Website Security Plans and Pricing*. Recuperat de: https://www.sitelock.com/pricing/

15. **Parlament Europeu / Consell de la UE** (2022). *Directiva NIS2 (2022/2555) relativa a mesures d'un alt nivell comú de ciberseguretat a la Unió*. Diari Oficial de la UE. Recuperat de: https://eur-lex.europa.eu/legal-content/CA/TXT/?uri=CELEX:32022L2555

