import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';

export default class Nav extends PureComponent {

    render() {
        const { active_nav, hammock } = this.props;
        return (
            <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
                <div className="uk-navbar-left">
                    <ul className="uk-navbar-nav hammock-navbar">
                        <li className={active_nav === 'general' ? 'uk-active' : '' }>
                            <Link to="/"><span>{hammock.page_strings.tabs.general}</span></Link>
                        </li>
                        <li className={active_nav === 'gateways' ? 'uk-active' : '' }>
                            <Link to="/gateways"><span>{hammock.page_strings.tabs.gateways}</span></Link>
                        </li>
                    </ul>
                </div>
            </nav>
        );
    }
}