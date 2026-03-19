{**
 * ClearDeal - About & Support Tab Template
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
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
                    <a href="https://github.com/GajewskiMarcin/ClearDeal/blob/main/LICENSE" target="_blank">GPL-3.0</a>
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
                <a href="https://www.buymeacoffee.com/marcingajewski" target="_blank" class="cleardeal-btn-coffee">
                    <i class="material-icons">local_cafe</i>
                    {l s='Buy me a coffee' mod='cleardeal'}
                </a>
                <a href="https://github.com/GajewskiMarcin/ClearDeal" target="_blank" class="cleardeal-btn-star">
                    <i class="material-icons">star</i>
                    {l s='Star on GitHub' mod='cleardeal'}
                </a>
            </div>
        </div>
    </div>

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
