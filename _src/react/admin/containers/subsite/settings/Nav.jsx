import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';

export default class Nav extends PureComponent {

    render() {
        const { active_nav, hubloy_membership } = this.props;
        return (
            <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
                <div className="uk-navbar-left">
                    <ul className="uk-navbar-nav hubloy_membership-navbar">
                        <li className={active_nav === 'general' ? 'uk-active' : '' }>
                            <Link to="/"><span>{hubloy_membership.page_strings.tabs.general}</span></Link>
                        </li>
                        <li className={active_nav === 'gateways' ? 'uk-active' : '' }>
                            <Link to="/gateways"><span>{hubloy_membership.page_strings.tabs.gateways}</span></Link>
                        </li>
                    </ul>
                </div>
            </nav>
        );
    }
}