import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';

export default class Nav extends PureComponent {

    render() {
        const { active_nav, hubloy_membership } = this.props;
        return (
            <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
                <div className="uk-navbar-left">
                    <ul className="uk-navbar-nav hubloy_membership-navbar">
                        {hubloy_membership.page_strings.tabs.map(tab =>
                            <li key={tab.id} className={active_nav === tab.id ? 'uk-active' : '' }>
                                <Link to={"/" + tab.url}><span>{tab.name}</span></Link>
                            </li>
                        )}
                    </ul>
                </div>
            </nav>
        );
    }
}