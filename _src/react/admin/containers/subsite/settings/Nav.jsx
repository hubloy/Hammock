import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';

export default class Nav extends PureComponent {

    render() {
        const { active_nav, hammock } = this.props;
        return (
            <div className="uk-width-1-4 uk-height-medium">
                <h2 className="uk-heading-divider">{hammock.common.string.title}</h2>
                <ul className="uk-nav-default hammock-switcher-nav uk-nav-parent-icon" uk-nav="">
                    <li className={active_nav === 'general' ? 'uk-active' : '' }>
                        <Link to="/" className="hammock-nav-button hammock-general uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{hammock.page_strings.tabs.general}</span></Link>
                    </li>
                    <li className={active_nav === 'gateways' ? 'uk-active' : '' }>
                        <Link to="/gateways" className="hammock-nav-button hammock-gateways uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{hammock.page_strings.tabs.gateways}</span></Link>
                    </li>
                </ul>
            </div>
        );
    }
}