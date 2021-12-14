import React, { PureComponent } from 'react';
import { Link } from 'react-router-dom';
import fetchWP from 'utils/fetchWP'

export default class Nav extends PureComponent {

    constructor(props) {
		super(props);
        this.state = {
			items : [],
			loading : true
		};
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

    async componentDidMount() {
		this.load_nav()
	}

    load_nav = async () => {
        this.fetchWP.get( 'rules/list' )
            .then( (json) => this.setState({
                items : json,
                loading : false
            }), (err) => this.setState({ loading : false })
        );
    }

    render() {
        const { active_nav } = this.props;
        var items = this.state.items;
        return (
            <div className='nav-menu'>
                {this.state.loading ? (
                    <span className="uk-text-center" uk-spinner="ratio: 2"></span>
                ) : (
                    <ul className="uk-nav uk-nav-default">
                        {Object.keys(items).map(item =>
                            <li className={active_nav === item ? 'uk-active' : '' } key={item}>
                                <Link to={"/" + item}><span>{items[item]}</span></Link>
                            </li>
                        )}
                    </ul>
                )}
                
            </div>
        );
    }
}