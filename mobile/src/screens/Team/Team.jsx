import { View } from "react-native";
import React, { useState } from "react";
import { Layout } from "../../shared";
import { Button } from "react-native-paper";
import { TeamList, TeamNotAssigned } from "../../components";

export const Team = () => {
  const STATUSES = {
    NOT_ASSIGNED: <TeamNotAssigned onPress={() => setStatus(STATUSES.ASSIGNED)} />,
    ASSIGNED: 'assigned',
    ACCEPTED: 'accepted'
  }
  const [status, setStatus] = useState(STATUSES.NOT_ASSIGNED);
  switch (status) {
    case STATUSES.ASSIGNED:
      return <Layout>
        <TeamList />
        <View style={styles.btnHolder}>
          <Button mode="outlined" raised onPress={() => setStatus(STATUSES.NOT_ASSIGNED)}>Бригада не готова к дежурству</Button>
          <Button mode="contained" style={styles.btn} onPress={() => setStatus(STATUSES.ACCEPTED)}>Бригада вышла на дежурство</Button>
        </View>
      </Layout>
    case STATUSES.ACCEPTED:
      return <Layout>
        <TeamList />
        <View style={styles.btnHolder}>
          <Button mode="contained" style={styles.btn} onPress={() => setStatus(STATUSES.NOT_ASSIGNED)}>Завершить</Button>
        </View>
      </Layout>
    default:
      return (
        STATUSES.NOT_ASSIGNED
      )
  }
}

const styles = {
  btnHolder: {
    marginTop: 'auto',
  },
  btn: {
    marginTop: 16
  }
}
