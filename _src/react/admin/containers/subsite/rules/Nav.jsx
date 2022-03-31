import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';

export default class Nav extends PureComponent {

    render() {
        const { active_nav, hubloy_membership, rules } = this.props;
        return (
            <nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
                <div className="uk-navbar-left">
                    <ul className="uk-navbar-nav hubloy_membership-navbar">
                        <li className={active_nav === '' ? 'uk-active' : '' } uk-filter-control=""><Link to={"/"}><span>{hubloy_membership.common.general.all}</span></Link></li>
                        {Object.keys(rules).map(item =>
                            <li className={active_nav === item ? 'uk-active' : '' } key={item}>
                                <Link to={"/" + item}><span>{rules[item]}</span></Link>
                            </li>
                        )}
                    </ul>
                </div>
            </nav>
        );
    }
}