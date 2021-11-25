import React from 'react';
import { BrowserRouter,Link } from 'react-router-dom';

export function PaginationUI(props) {
    const { pager } = props;
    return(
        <React.Fragment>
        {pager.pages && pager.pages.length > 0 &&
            <ul className="uk-pagination uk-flex-right" uk-margin="">
                <li className={`${pager.current === 1 ? 'uk-disabled' : ''}`}>
                    <Link to={{  pathname: `/page/1` }}><span uk-icon="chevron-double-left"></span></Link>
                </li>
                <li className={`${pager.current === 1 ? 'uk-disabled' : ''}`}>
                    <Link to={{ pathname: `/page/${pager.current - 1}` }}><span uk-icon="chevron-left"></span></Link>
                </li>
                {pager.pages.map(page =>
                    <li key={page} className={`${pager.current === page ? 'uk-active' : ''}`}>
                        {parseInt(Number(page)) > 0 ? (
                            <Link to={{pathname : `/page/${page}` }}>{page}</Link>
                        ): (
                            <span>{page}</span>
                        )}
                    </li>
                )}
                <li className={`${pager.current === pager.total ? 'uk-disabled' : ''}`}>
                    <Link to={{ pathname: `/page/${pager.current + 1}` }}><span uk-icon="chevron-right"></span></Link>
                </li>
                <li className={`${pager.current === pager.total ? 'uk-disabled' : ''}`}>
                    <Link to={{ pathname: `/page/${pager.total}` }}><span uk-icon="chevron-double-right"></span></Link>
                </li>
            </ul>
        }
        </React.Fragment>
    )
}