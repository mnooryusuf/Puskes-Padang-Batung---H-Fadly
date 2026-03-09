<?php

/**
 * IDE Helper - JANGAN DIEKSEKUSI.
 * File ini hanya untuk Intelephense agar mengenali tipe kembalian auth().
 *
 * @noinspection ALL
 */

namespace {

    /**
     * @return \Illuminate\Contracts\Auth\Factory|\App\Models\User|null
     */
    function auth() {}
}

namespace Illuminate\Contracts\Auth {

    interface Guard
    {
        /**
         * @return \App\Models\User|null
         */
        public function user();
    }

    interface StatefulGuard extends Guard {}
}

namespace Illuminate\Auth {

    /**
     * @method \App\Models\User|null user()
     */
    class SessionGuard {}
}
