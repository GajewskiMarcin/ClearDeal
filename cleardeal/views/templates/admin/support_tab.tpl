{**
 * ClearDeal - About & Support Tab Template
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License 3.0 (AFL-3.0)
 *}

<div class="cleardeal-about">
    {* Module Info Section *}
    <div class="cleardeal-about-section">
        <div class="cleardeal-about-section-header">
            <i class="material-icons">info</i>
            <h3>{l s='Module Information' mod='cleardeal'}</h3>
        </div>

        <div class="cleardeal-about-info-grid">
            <div class="cleardeal-about-info-item">
                <span class="cleardeal-about-info-label">{l s='Module Name' mod='cleardeal'}</span>
                <span class="cleardeal-about-info-value">ClearDeal</span>
            </div>
            <div class="cleardeal-about-info-item">
                <span class="cleardeal-about-info-label">{l s='Version' mod='cleardeal'}</span>
                <span class="cleardeal-about-info-value">{$module_version}</span>
            </div>
            <div class="cleardeal-about-info-item">
                <span class="cleardeal-about-info-label">{l s='Author' mod='cleardeal'}</span>
                <span class="cleardeal-about-info-value">
                    <a href="https://marcingajewski.pl" target="_blank">marcingajewski.pl</a>
                </span>
            </div>
            <div class="cleardeal-about-info-item">
                <span class="cleardeal-about-info-label">{l s='License' mod='cleardeal'}</span>
                <span class="cleardeal-about-info-value">
                    <a href="https://github.com/GajewskiMarcin/ClearDeal/blob/main/LICENSE" target="_blank">AFL-3.0</a>
                </span>
            </div>
        </div>

        <div class="cleardeal-about-hooks">
            <h4>{l s='Registered Hooks' mod='cleardeal'}</h4>
            <p class="cleardeal-about-hooks-desc">{l s='This module is registered on the following hooks:' mod='cleardeal'}</p>
            <div class="cleardeal-about-hooks-grid">
                <div class="cleardeal-about-hook-group">
                    <span class="cleardeal-about-hook-group-title">{l s='Display Hooks' mod='cleardeal'}</span>
                    <ul class="cleardeal-about-hook-list">
                        <li><code>displayHeader</code></li>
                        <li><code>displayProductPriceBlock</code></li>
                        <li><code>displayProductAdditionalInfo</code></li>
                        <li><code>displayProductListReviews</code></li>
                    </ul>
                </div>
                <div class="cleardeal-about-hook-group">
                    <span class="cleardeal-about-hook-group-title">{l s='Action Hooks' mod='cleardeal'}</span>
                    <ul class="cleardeal-about-hook-list">
                        <li><code>actionProductAdd</code></li>
                        <li><code>actionProductUpdate</code></li>
                        <li><code>actionObjectSpecificPriceAddAfter</code></li>
                        <li><code>actionObjectSpecificPriceUpdateAfter</code></li>
                        <li><code>actionObjectSpecificPriceDeleteAfter</code></li>
                    </ul>
                </div>
                <div class="cleardeal-about-hook-group">
                    <span class="cleardeal-about-hook-group-title">{l s='Custom Hooks' mod='cleardeal'}</span>
                    <ul class="cleardeal-about-hook-list">
                        <li><code>displayClearDealProduct</code></li>
                        <li><code>displayClearDealListing</code></li>
                        <li><code>displayClearDealQuickView</code></li>
                        <li><code>actionCreativeElementsInit</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {* Getting Help Section *}
    <div class="cleardeal-about-section">
        <div class="cleardeal-about-section-header">
            <i class="material-icons">help_outline</i>
            <h3>{l s='Getting Help' mod='cleardeal'}</h3>
        </div>

        <div class="cleardeal-about-cards">
            <div class="cleardeal-about-card">
                <div class="cleardeal-about-card-icon cleardeal-about-card-icon-blue">
                    <i class="material-icons">menu_book</i>
                </div>
                <h4>{l s='Documentation' mod='cleardeal'}</h4>
                <p>{l s='User guides and tutorials on GitHub Wiki' mod='cleardeal'}</p>
                <a href="https://github.com/GajewskiMarcin/ClearDeal/wiki" target="_blank" class="cleardeal-about-card-btn">
                    <i class="material-icons">open_in_new</i>
                    {l s='Read Wiki' mod='cleardeal'}
                </a>
            </div>

            <div class="cleardeal-about-card">
                <div class="cleardeal-about-card-icon cleardeal-about-card-icon-green">
                    <i class="material-icons">bug_report</i>
                </div>
                <h4>{l s='GitHub Issues' mod='cleardeal'}</h4>
                <p>{l s='Report bugs or request features' mod='cleardeal'}</p>
                <a href="https://github.com/GajewskiMarcin/ClearDeal/issues" target="_blank" class="cleardeal-about-card-btn">
                    <i class="material-icons">add_circle_outline</i>
                    {l s='Report Issue' mod='cleardeal'}
                </a>
            </div>

            <div class="cleardeal-about-card">
                <div class="cleardeal-about-card-icon cleardeal-about-card-icon-purple">
                    <i class="material-icons">forum</i>
                </div>
                <h4>{l s='Discussions' mod='cleardeal'}</h4>
                <p>{l s='Join the community on GitHub' mod='cleardeal'}</p>
                <a href="https://github.com/GajewskiMarcin/ClearDeal/discussions" target="_blank" class="cleardeal-about-card-btn">
                    <i class="material-icons">chat</i>
                    {l s='Join Discussion' mod='cleardeal'}
                </a>
            </div>
        </div>
    </div>

    {* Support Project Section *}
    <div class="cleardeal-about-support">
        <div class="cleardeal-about-support-content">
            <h3>{l s='Support the Project' mod='cleardeal'}</h3>
            <p>{l s='If you find ClearDeal useful, consider supporting its development' mod='cleardeal'}</p>
            <div class="cleardeal-about-support-buttons">
                <a href="https://ko-fi.com/quinceproject" target="_blank" class="cleardeal-btn-coffee">
                    <i class="material-icons">local_cafe</i>
                    {l s='Support on Ko-fi' mod='cleardeal'}
                </a>
                <a href="https://github.com/GajewskiMarcin/ClearDeal" target="_blank" class="cleardeal-btn-star">
                    <i class="material-icons">star</i>
                    {l s='Star on GitHub' mod='cleardeal'}
                </a>
            </div>
        </div>
    </div>

    {* Quince Project: the author's theme and module suite (views/css/quince-promo.css) *}
    <section class="quince-promo" style="margin: 0 0 24px;">
        <div class="quince-promo-wash" aria-hidden="true"></div>
        <i class="quince-promo-beam b1" aria-hidden="true"></i><i class="quince-promo-beam b2" aria-hidden="true"></i><i class="quince-promo-beam b3" aria-hidden="true"></i><i class="quince-promo-beam b4" aria-hidden="true"></i>
        <div class="quince-promo-in">
            <p class="quince-promo-lead">{l s='Discover the theme and engine for PrestaShop' mod='cleardeal'}</p>
            <p class="quince-promo-word">Quince Project</p>
            <p class="quince-promo-text">{l s='A set of over 20 modules and a theme that let you freely manage the look and content of your shop and support back-office processes.' mod='cleardeal'}</p>
            <div class="quince-promo-cta">
                <a class="quince-promo-btn" href="https://github.com/quinceproject" target="_blank" rel="noopener">
                    <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"/></svg>
                    {l s='Download for free from GitHub' mod='cleardeal'}
                </a>
            </div>
            <div class="quince-promo-facts">
                <div><b>PrestaShop 9.2</b></div>
                <div><b>{l s='PHP 8.1 or newer' mod='cleardeal'}</b></div>
                <div><b>{l s='OSL-3.0 licence' mod='cleardeal'}</b></div>
            </div>
        </div>
    </section>

    {* System Info Section *}
    <div class="cleardeal-about-system">
        <button type="button" class="cleardeal-about-system-toggle" id="system_info_toggle">
            <i class="material-icons">terminal</i>
            <span>{l s='System Information' mod='cleardeal'}</span>
            <i class="material-icons cleardeal-about-system-arrow">expand_more</i>
        </button>
        <div class="cleardeal-about-system-content" id="system_info_content" style="display: none;">
            <table class="cleardeal-about-system-table">
                <tr>
                    <td>{l s='PrestaShop Version' mod='cleardeal'}</td>
                    <td><strong>{$smarty.const._PS_VERSION_}</strong></td>
                </tr>
                <tr>
                    <td>{l s='PHP Version' mod='cleardeal'}</td>
                    <td><strong>{$smarty.const.PHP_VERSION}</strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>
