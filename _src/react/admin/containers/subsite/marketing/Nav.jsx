import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';

export default class Nav extends PureComponent {

    render() {
        const { active_nav, hammock } = this.props;
        return (
            <div>
                <h2 className="uk-heading-divider">{hammock.common.string.title}</h2>
                <ul className="uk-nav-default hammock-switcher-nav uk-nav-parent-icon" uk-nav="">
                    {Object.entries(hammock.strings.nav).map((type, index) => {
                        return (<li key={index} className={active_nav === type[0] ? 'uk-active' : '' }>
                            <Link to={"/" + type[0]} className="hammock-nav-button hammock-general uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{type[1]}</span></Link>
                        </li>)
                    })}
                </ul>
            </div>
        );
    }
}